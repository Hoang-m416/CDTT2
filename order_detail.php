<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// ✅ Lấy đúng order_id
$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    echo "ID đơn hàng không hợp lệ.";
    exit;
}

// ✅ Truy vấn đơn hàng và thông tin khách hàng
$order_stmt = $conn->prepare("
    SELECT o.id, c.full_name, c.phone, o.order_date, o.delivery_address, o.payment_method, o.shipping_fee, o.total_amount, o.status
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    WHERE o.id = ?
");
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();

if (!$order) {
    echo "Không tìm thấy đơn hàng với ID = $order_id";
    exit;
}

// ✅ Truy vấn sản phẩm trong đơn hàng, bao gồm size
$item_stmt = $conn->prepare("
    SELECT p.name, oi.size, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-3">
            <?php include 'includes/account_sidebar.php'; ?>
        </div>

        <div class="col-md-9">
            <div class="bg-white p-4 rounded shadow-sm">
                <h2>🧾 Chi tiết đơn hàng #<?= $order_id ?></h2>
                <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                <p><strong>Địa chỉ giao:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                <p><strong>Phí vận chuyển:</strong> <?= number_format($order['shipping_fee'], 0, ',', '.') ?> ₫</p>
                <p><strong>Trạng thái:</strong> <?= htmlspecialchars($order['status']) ?></p>

                <h5 class="mt-4">📦 Sản phẩm</h5>
                <table class="table table-bordered mt-2">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tạm tính</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $subtotal = 0; ?>
                        <?php while ($item = $item_result->fetch_assoc()): ?>
                            <?php 
                                $line_total = $item['quantity'] * $item['price'];
                                $subtotal += $line_total;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['size']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                                <td><?= number_format($line_total, 0, ',', '.') ?> ₫</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Tạm tính:</th>
                            <td><?= number_format($subtotal, 0, ',', '.') ?> ₫</td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Phí vận chuyển:</th>
                            <td><?= number_format($order['shipping_fee'], 0, ',', '.') ?> ₫</td>
                        </tr>
                        <?php 
                            $calculated_total = $subtotal + $order['shipping_fee'];
                            $discount = $calculated_total - $order['total_amount'];
                        ?>
                        <tr>
                            <th colspan="4" class="text-end text-success">Giảm giá:</th>
                            <td class="text-success">-<?= number_format($discount, 0, ',', '.') ?> ₫</td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Tổng cộng:</th>
                            <td><strong><?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</strong></td>
                        </tr>
                    </tfoot>
                </table>

                <a href="orders.php" class="btn btn-warning mt-3">← Quay lại đơn hàng</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
