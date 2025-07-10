<?php
session_start();

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer']['id'];
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// Hủy đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_order_id = (int) $_POST['cancel_order_id'];

    // Kiểm tra trạng thái và phương thức thanh toán của đơn hàng
    $stmt = $conn->prepare("SELECT status, payment_method FROM orders WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $cancel_order_id, $customer_id);
    $stmt->execute();
    $stmt->bind_result($cancel_status, $payment_method);
    $stmt->fetch();
    $stmt->close();

    // Không cho phép hủy nếu là đơn thanh toán qua momo
    if (strtolower($payment_method) === 'momo') {
        echo "<script>showToast('❌ Không thể hủy đơn thanh toán bằng MoMo');</script>";
    }
    elseif (in_array($cancel_status, ['Chờ xác nhận', 'Đang xử lý', 'Đang chuẩn bị hàng'])) {
        // Cập nhật trạng thái đơn
        $stmt = $conn->prepare("UPDATE orders SET status = 'Đã hủy' WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("ii", $cancel_order_id, $customer_id);
        $stmt->execute();
        $stmt->close();

        // Lấy thông tin sản phẩm trong đơn để hoàn tồn
        $item_stmt = $conn->prepare("
            SELECT oi.product_id, oi.size, oi.quantity
            FROM order_items oi
            WHERE oi.order_id = ?
        ");
        $item_stmt->bind_param("i", $cancel_order_id);
        $item_stmt->execute();
        $items_result = $item_stmt->get_result();

        while ($item = $items_result->fetch_assoc()) {
            $product_id = $item['product_id'];
            $size = $item['size'];
            $quantity = $item['quantity'];

            // Cập nhật lại số lượng trong kho
            $update_stock_stmt = $conn->prepare("
                UPDATE product_sizes
                SET quantity = quantity + ?
                WHERE product_id = ? AND size = ?
            ");
            $update_stock_stmt->bind_param("iis", $quantity, $product_id, $size);
            $update_stock_stmt->execute();
            $update_stock_stmt->close();
        }
        $item_stmt->close();

        echo "<script>showToast('✅ Đã hủy đơn hàng thành công');</script>";
    }
}




// Xác nhận đã nhận hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int) $_POST['order_id'];
    $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $order_id, $customer_id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    if ($current_status === 'Đã gửi đơn vận chuyển') {
        $stmt = $conn->prepare("UPDATE orders SET status = 'Đã giao thành công' WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Lấy danh sách đơn hàng
$stmt = $conn->prepare("SELECT id, order_date, total_amount, status FROM orders WHERE customer_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $order_date, $total_amount, $status);

// Lấy danh sách sản phẩm đã đánh giá
$rating_stmt = $conn->prepare("SELECT product_id FROM product_ratings WHERE customer_id = ?");
$rating_stmt->bind_param("i", $customer_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();

$rated_products = [];
while ($row = $rating_result->fetch_assoc()) {
    $rated_products[] = $row['product_id'];
}
$rating_stmt->close();

function statusBadge($status) {
    switch ($status) {
        case 'Chờ xác nhận': return '<span class="badge bg-secondary">'.$status.'</span>';
        case 'Đang xử lý': return '<span class="badge bg-info text-dark">'.$status.'</span>';
        case 'Đang chuẩn bị hàng': return '<span class="badge bg-warning text-dark">'.$status.'</span>';
        case 'Đã gửi đơn vận chuyển': return '<span class="badge bg-primary">'.$status.'</span>';
        case 'Đã giao thành công': return '<span class="badge bg-success">'.$status.'</span>';
        case 'Đã hủy': return '<span class="badge bg-danger">'.$status.'</span>';
        default: return $status;
    }
}
?>
<?php
$stmt = $conn->prepare("SELECT id, order_date, total_amount, status, payment_method FROM orders WHERE customer_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $order_date, $total_amount, $status, $payment_method);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của bạn - SportShop</title>

    <link rel="stylesheet" href="assets/css/style.css"> <!-- File style riêng của bạn -->
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-5 mt-5">
<div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'includes/account_sidebar.php'; ?>
        </div>
       <!-- Main content -->
    <div class="col-md-9">
    <div class="bg-white p-4 rounded shadow-sm">
        <h2>📦 Đơn hàng của bạn</h2>
        <table class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Xem chi tiết</th> <!-- Cột mới -->
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
<?php while ($stmt->fetch()): ?>
    <tr>
        <td>#<?= $id ?></td>
        <td><?= $order_date ?></td>
        <td><?= number_format($total_amount, 0, ',', '.') ?> ₫</td>
        <td><?= statusBadge($status) ?></td>

        <!-- Xem chi tiết -->
        <td>
            <a href="order_detail.php?order_id=<?= $id ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
        </td>

        <!-- Thao tác -->
        <td>
            <?php if ($status === 'Đã gửi đơn vận chuyển'): ?>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="order_id" value="<?= $id ?>">
                    <button type="submit" class="btn btn-success btn-sm">Đã nhận hàng</button>
                </form>

            <?php elseif ($status === 'Đã giao thành công'): ?>
                <?php
                $products_stmt = $conn->prepare("
                    SELECT p.id, p.name 
                    FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?
                ");
                $products_stmt->bind_param("i", $id);
                $products_stmt->execute();
                $products_result = $products_stmt->get_result();

                while ($row = $products_result->fetch_assoc()):
                    $product_id = $row['id'];
                    $product_name = $row['name'];

                    $check_stmt = $conn->prepare("SELECT id FROM product_ratings WHERE order_id = ? AND product_id = ? AND customer_id = ?");
                    $check_stmt->bind_param("iii", $id, $product_id, $_SESSION['customer_id']);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    $has_rating = $check_result->num_rows > 0;
                    $check_stmt->close();
                ?>

                    <?php if ($has_rating): ?>
                        <a href="view_rating.php?product_id=<?= $product_id ?>&order_id=<?= $id ?>" class="btn btn-outline-info btn-sm my-1">
                            <?= htmlspecialchars($product_name) ?>: Xem đánh giá
                        </a><br>
                    <?php else: ?>
                        <a href="rating.php?product_id=<?= $product_id ?>&order_id=<?= $id ?>" class="btn btn-warning btn-sm my-1">
                            <?= htmlspecialchars($product_name) ?>: Đánh giá
                        </a><br>
                    <?php endif; ?>
                <?php endwhile; ?>
                <?php $products_stmt->close(); ?>

            <?php elseif (
                in_array($status, ['Đã thanh toán','Chờ xác nhận', 'Đang xử lý', 'Đang chuẩn bị hàng'])
            ): ?>
                <?php if (strtolower($payment_method) === 'momo'): ?>
                    <span class="text-muted">Đã thanh toán không thể hủy</span>
                <?php else: ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="cancel_order_id" value="<?= $id ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hủy đơn hàng</button>
                    </form>
                <?php endif; ?>

            <?php else: ?>
                —
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>
</tbody>

    </table>
    </div>
</div>

</div>
</div>


<!-- Toast thông báo -->
<div id="toast" style="
    visibility: hidden;
    min-width: 300px;
    max-width: 90%;
    background-color: rgb(251, 251, 251);
    border: 2px solid rgb(230, 233, 83);
    color: rgb(23, 23, 23);
    text-align: center;
    border-radius: 15px;
    padding: 18px 28px 24px;
    position: fixed;
    z-index: 9999;
    top: 40px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 20px;
    transition: visibility 0s, opacity 0.5s linear;
    opacity: 0;
    box-shadow: 0 4px 12px rgba(2, 255, 230, 0.39);
    overflow: hidden;
">
    <div id="toastProgress" style="
        position: absolute;
        top: 0;
        left: 0;
        height: 5px;
        background-color: rgb(10, 113, 161);
        width: 0%;
    "></div>
</div>
</div>



<?php include 'includes/footer.php'; ?>
<?php include 'chatbox.php'; ?>
</body>
</html>


<style>
/* Đơn hàng - bảng chính */
.table {
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
    font-size: 16px;
}

.table thead th {
    background-color: #f8f9fa;
    color: #333;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
}

.table tbody td {
    vertical-align: middle;
    text-align: center;
}

/* Button tùy chỉnh nhỏ gọn hơn */
.btn-sm {
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 8px;
}

/* Huy, Danh gia, Da nhan hang */
.btn-danger {
    background-color: #e74c3c;
    border-color: #e74c3c;
}

.btn-success {
    background-color: #2ecc71;
    border-color: #2ecc71;
}

.btn-warning {
    background-color: #f39c12;
    border-color: #f39c12;
}

.btn-outline-info {
    color: #17a2b8;
    border-color: #17a2b8;
}

.btn-outline-info:hover {
    background-color: #17a2b8;
    color: #fff;
}

/* Badge màu sắc trạng thái */
.badge {
    padding: 6px 10px;
    font-size: 14px;
    border-radius: 12px;
}

.bg-secondary { background-color: #6c757d !important; }
.bg-info { background-color: #17a2b8 !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-primary { background-color: #007bff !important; }
.bg-success { background-color: #28a745 !important; }
.bg-danger { background-color: #dc3545 !important; }

.text-dark { color: #212529 !important; }

/* Khối nội dung đơn hàng */
.bg-white.p-4.rounded.shadow-sm {
    border: 1px solid #eaeaea;
    background: #fff;
}

/* Responsive */
@media screen and (max-width: 768px) {
    .table {
        font-size: 14px;
    }

    .btn-sm {
        font-size: 12px;
        padding: 4px 8px;
    }
}

</style>


<script>
function showToast(message, duration = 2000) {
    const toast = document.getElementById('toast');
    const progress = document.getElementById('toastProgress');

    toast.textContent = message;
    toast.style.visibility = 'visible';
    toast.style.opacity = 1;
    progress.style.transition = `width ${duration}ms linear`;
    progress.style.width = '60%';

    setTimeout(() => {
        toast.style.opacity = 0;
        progress.style.transition = 'none';
        progress.style.width = '0%';
        setTimeout(() => toast.style.visibility = 'hidden', 500);
    }, duration);
}

window.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        showToast('✅ Đặt hàng thành công!');
        history.replaceState({}, document.title, location.pathname); // Xoá ?success=1 khỏi URL
    }
});
</script>
