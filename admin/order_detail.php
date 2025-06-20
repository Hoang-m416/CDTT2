<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$order_id = $_GET['id'] ?? 0;

// Lấy thông tin đơn hàng
$order_stmt = $conn->prepare("SELECT o.id, c.full_name, c.phone, o.order_date, o.delivery_address, o.payment_method, o.shipping_fee, o.total_amount, o.status
                              FROM orders o
                              JOIN customers c ON o.customer_id = c.id
                              WHERE o.id = ?");
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();

// Lấy chi tiết sản phẩm trong đơn
$item_stmt = $conn->prepare("SELECT p.name, oi.quantity, oi.price
                             FROM order_items oi
                             JOIN products p ON oi.product_id = p.id
                             WHERE oi.order_id = ?");
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $order_id ?></title>
    <link rel="stylesheet" href="admin_style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8f9fa; color: #333; }
        .main { margin-left: 220px; padding: 30px; background: #fff; min-height: 100vh; }
        h3, h5 { color: #2c3e50; margin-bottom: 20px; }
        .order-info p { margin-bottom: 6px; }
        .table th, .table td { vertical-align: middle !important; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100%; width: 220px; background: rgb(69, 50, 193); padding-top: 60px; }
        .sidebar a { color: #fff; padding: 15px 20px; display: block; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; }
        .badge, .btn { font-size: 0.9rem; }
        .card.order-info { background: #f1f1f1; border-left: 5px solid #0d6efd; }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main">
    <h3>Chi tiết đơn hàng #<?= $order_id ?></h3>

    <?php if (!$order): ?>
        <div class="alert alert-danger mt-4">Không tìm thấy đơn hàng.</div>
    <?php else: ?>
        <div class="card p-3 order-info mb-4">
            <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            <p><strong>Ngày đặt:</strong> <?= $order['order_date'] ?></p>
            <p><strong>Địa chỉ giao:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
            <p><strong>Phương thức thanh toán:</strong> <?= $order['payment_method'] ?></p>
            <p><strong>Phí vận chuyển:</strong> <?= number_format($order['shipping_fee'], 0, ',', '.') ?> ₫</p>
            <p><strong>Trạng thái:</strong>
                <?php
                switch ($order['status']) {
                    case 'Đã giao thành công':
                        echo '<span class="badge bg-success">Đã giao thành công</span>';
                        break;
                    case 'Đã hủy':
                        echo '<span class="badge bg-danger">Đã hủy</span>';
                        break;
                    default:
                        echo '<span class="badge bg-warning text-dark">' . $order['status'] . '</span>';
                }
                ?>
            </p>
        </div>

        <h5>Sản phẩm</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="table-primary">
                    <th>Sản phẩm</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Giá</th>
                    <th class="text-end">Tạm tính</th>
                </tr>
            </thead>
            <tbody>
                <?php $subtotal = 0; ?>
                <?php while ($item = $item_result->fetch_assoc()): ?>
                    <?php $line_total = $item['quantity'] * $item['price']; ?>
                    <?php $subtotal += $line_total; ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                        <td class="text-end"><?= number_format($line_total, 0, ',', '.') ?> ₫</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        <tfoot>
    <tr>
        <th colspan="3" class="text-end">Tạm tính:</th>
        <td class="text-end"><?= number_format($subtotal, 0, ',', '.') ?> ₫</td>
    </tr>
    <tr>
        <th colspan="3" class="text-end">Phí vận chuyển:</th>
        <td class="text-end"><?= number_format($order['shipping_fee'], 0, ',', '.') ?> ₫</td>
    </tr>
    <?php
        $calculated_total = $subtotal + $order['shipping_fee'];
        $discount = $calculated_total - $order['total_amount'];
    ?>
    <tr>
        <th colspan="3" class="text-end">Giảm giá:</th>
        <td class="text-end text-danger">- <?= number_format($discount, 0, ',', '.') ?> ₫</td>
    </tr>
    <tr class="table-secondary">
        <th colspan="3" class="text-end">Tổng cộng:</th>
        <td class="text-end"><strong><?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</strong></td>
    </tr>
</tfoot>

        </table>

        <a href="admin_orders.php" class="btn btn-secondary mt-3">← Quay lại danh sách đơn hàng</a>
       
    <?php endif; ?>
</div>
</body>
</html>
