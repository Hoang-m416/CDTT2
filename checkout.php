<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$customer_id = $_SESSION['customer_id'];

// Lấy thông tin khách hàng
$stmt = $conn->prepare("SELECT full_name, email, phone, address, province FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $phone, $default_address, $default_province);
$stmt->fetch();
$stmt->close();

// Tính tổng giỏ hàng
$total = 0;
$cart = $_SESSION['cart'] ?? [];
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Lấy địa chỉ giao hàng
$delivery_address = $_POST['delivery_address'] ?? $default_address;
$use_different_address = isset($_POST['use_different_address']);

// Tính phí ship
$shipping_fee = 0;
if ($total <= 1000000) {
    $stmt = $conn->prepare("SELECT fee FROM shipping_fee WHERE province = ?");
    $stmt->bind_param("s", $default_province);
    $stmt->execute();
    $stmt->bind_result($shipping_fee);
    $stmt->fetch();
    $stmt->close();
}

// Tính tổng chi tiêu
$total_spent = 0;
$stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders WHERE customer_id = ? AND status = 'Đã giao thành công'");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($total_spent);
$stmt->fetch();
$stmt->close();
$total_spent = $total_spent ?? 0;

// Xác định hạng thành viên
$tier = "Đồng";
$benefits = ["Ưu đãi đặc biệt khi lên hạng"];
$discount_percent = 0;

if ($total_spent >= 60000000) {
    $tier = "Kim Cương";
    $benefits = ["Miễn phí vận chuyển", "Giảm 10% mỗi đơn", "Quà tặng sinh nhật"];
    $discount_percent = 10;
} elseif ($total_spent >= 10000000) {
    $tier = "Vàng";
    $benefits = ["Miễn phí vận chuyển", "Giảm 5% mỗi đơn"];
    $discount_percent = 5;
} elseif ($total_spent >= 1000000) {
    $tier = "Bạc";
    $benefits = ["Giảm 2% mỗi đơn"];
    $discount_percent = 2;
}

// Áp dụng giảm giá
$discount_amount = $total * $discount_percent / 100;
$grand_total = $total - $discount_amount + $shipping_fee;
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - SportShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> h3 { padding-top: 70px; } </style>
</head>
<body class="bg-light">
<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <h3 class="mb-4 text-center">Xác nhận đơn hàng</h3>

    <!-- Thông tin khách hàng -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Thông tin khách hàng</h5>
        </div>
        <div class="card-body">
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($full_name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>SĐT:</strong> <?= htmlspecialchars($phone) ?></p>
            <p><strong>Địa chỉ mặc định:</strong> <?= htmlspecialchars($default_address) ?></p>
            <p><strong>Tỉnh/thành:</strong> <?= htmlspecialchars($default_province) ?></p>

            <hr>
            <h6>🎖 <strong>Hạng thành viên:</strong> <?= $tier ?></h6>
            <p><strong>Ưu đãi của bạn:</strong></p>
            <ul>
                <?php foreach ($benefits as $b): ?>
                    <li><?= htmlspecialchars($b) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Địa chỉ giao hàng -->
    <form method="POST" action="checkout.php" class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Địa chỉ giao hàng</h5>
        </div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="use_different_address" id="use_different_address"
                    <?= $use_different_address ? 'checked' : '' ?> onchange="this.form.submit()">
                <label class="form-check-label" for="use_different_address">
                    Giao hàng đến địa chỉ khác
                </label>
            </div>

            <?php if ($use_different_address): ?>
                <div class="mb-3">
                    <label for="delivery_address" class="form-label">Địa chỉ giao hàng mới:</label>
                    <textarea name="delivery_address" id="delivery_address" class="form-control" rows="3" required><?= htmlspecialchars($delivery_address) ?></textarea>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-outline-primary">Cập nhật địa chỉ</button>
        </div>
    </form>

    <!-- Danh sách sản phẩm -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Sản phẩm trong đơn</h5>
        </div>
        <div class="card-body">
            <?php foreach ($cart as $item): ?>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <div>
                        <?= htmlspecialchars($item['name']) ?>
                        <small class="text-muted">(Size: <?= htmlspecialchars($item['size']) ?>)</small>
                    </div>
                    <div>x<?= $item['quantity'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Thông tin thanh toán -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Thông tin thanh toán</h5>
        </div>
        <div class="card-body">
            <p class="mb-1">Tạm tính: <strong><?= number_format($total, 0, ',', '.') ?> ₫</strong></p>
            <?php if ($discount_amount > 0): ?>
                <p class="mb-1 text-success">Giảm giá (<?= $discount_percent ?>%): <strong>-<?= number_format($discount_amount, 0, ',', '.') ?> ₫</strong></p>
            <?php endif; ?>
            <p class="mb-1">Phí vận chuyển: <strong><?= number_format($shipping_fee, 0, ',', '.') ?> ₫</strong></p>
            <p class="fs-5 mt-2">Tổng thanh toán: <strong class="text-danger"><?= number_format($grand_total, 0, ',', '.') ?> ₫</strong></p>

            <!-- Phương thức thanh toán -->
            <form id="payment-form" method="POST" action="place_order.php">
                <input type="hidden" name="shipping_fee" value="<?= $shipping_fee ?>">
                <input type="hidden" name="delivery_address" value="<?= htmlspecialchars($delivery_address) ?>">
                <input type="hidden" name="use_different_address" value="<?= $use_different_address ? 1 : 0 ?>">
                <input type="hidden" name="discount_amount" value="<?= $discount_amount ?>">
                <input type="hidden" name="payment_method" id="selected_payment_method" value="COD">

                <div class="payment-options mb-3">
                    <label class="form-check-label me-3">
                        <input class="form-check-input" type="radio" name="payment" value="COD" checked onchange="updatePaymentMethod(this.value)"> 
                        Thanh toán khi nhận hàng (COD)
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="payment" value="Momo" onchange="updatePaymentMethod(this.value)"> 
                        Thanh toán bằng MoMo
                    </label>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <a href="cart.php" class="btn btn-outline-secondary">← Quay lại giỏ hàng</a>
                    <button type="submit" class="btn btn-primary">Đặt hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePaymentMethod(method) {
    document.getElementById('selected_payment_method').value = method;
}

document.getElementById("payment-form").addEventListener("submit", function (e) {
    const method = document.getElementById("selected_payment_method").value;
    if (method === "Momo") {
        const params = new URLSearchParams({
            amount: <?= $grand_total ?>,
            delivery_address: "<?= htmlspecialchars($delivery_address) ?>",
            use_different_address: <?= $use_different_address ? 1 : 0 ?>
        });
        window.location.href = "momo/prepare_payment.php?" + params.toString();
        e.preventDefault();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
