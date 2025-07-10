<?php 
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer']['id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = (int)$_SESSION['customer']['id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    die("❌ Giỏ hàng trống, không thể đặt hàng.");
}

// Kiểm tra sản phẩm có tồn tại không
$check_product_stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
foreach ($cart as $key => $item) {
    if (!isset($item['id'], $item['size'], $item['quantity'], $item['price'])) {
        die("❌ Sản phẩm $key trong giỏ hàng bị thiếu thông tin.");
    }
    $product_id = (int)$item['id'];
    $check_product_stmt->bind_param("i", $product_id);
    $check_product_stmt->execute();
    $check_product_stmt->store_result();
    if ($check_product_stmt->num_rows === 0) {
        die("❌ Sản phẩm ID $product_id không tồn tại.");
    }
}

// Lấy dữ liệu từ form
$shipping_fee = isset($_POST['shipping_fee']) ? (int)$_POST['shipping_fee'] : 0;
$discount_amount = isset($_POST['discount_amount']) ? (float)$_POST['discount_amount'] : 0;
$delivery_address = trim($_POST['delivery_address'] ?? '');
$payment_method = $_POST['payment_method'] ?? 'COD';
$status = ($payment_method === 'MoMo') ? 'Đã thanh toán' : 'Chờ xác nhận';

// Tính tổng đơn hàng sau khi trừ discount và cộng phí vận chuyển
$total_amount = 0;
foreach ($cart as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}
$total_amount = $total_amount - $discount_amount + $shipping_fee;

if ($total_amount < 0) {
    die("❌ Tổng đơn hàng không hợp lệ.");
}

$conn->begin_transaction();

try {
    // 1. Tạo đơn hàng
    $sql = "INSERT INTO orders (customer_id, order_date, delivery_address, payment_method, shipping_fee, total_amount, status)
            VALUES (?, NOW(), ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdis", $customer_id, $delivery_address, $payment_method, $shipping_fee, $total_amount, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // 2. Thêm vào bảng order_items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, size, quantity, price) VALUES (?, ?, ?, ?, ?)");
    foreach ($cart as $item) {
        $product_id = (int)$item['id'];
        $size = $item['size'];
        $quantity = (int)$item['quantity'];
        $price = (float)$item['price'];

        $stmt->bind_param("iisid", $order_id, $product_id, $size, $quantity, $price);
        $stmt->execute();
    }

    // 3. Trừ tồn kho
    $update_stmt = $conn->prepare("UPDATE product_sizes SET quantity = quantity - ? 
                                   WHERE product_id = ? AND size = ? AND quantity >= ?");
    foreach ($cart as $item) {
        $product_id = (int)$item['id'];
        $size = $item['size'];
        $qty = (int)$item['quantity'];

        $update_stmt->bind_param("iisi", $qty, $product_id, $size, $qty);
        $update_stmt->execute();

        if ($update_stmt->affected_rows === 0) {
            $conn->rollback();
            die("❌ Sản phẩm ID $product_id size $size không đủ hàng tồn.");
        }
    }

    // 4. Xóa giỏ hàng
    unset($_SESSION['cart']);
    $conn->commit();
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    // Chuyển hướng về trang đơn hàng kèm thông báo thành công
    header("Location: orders.php?success=1");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("❌ Lỗi khi đặt hàng: " . $e->getMessage());
}
?>
