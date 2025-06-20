<?php
session_start();

// 1. Kết nối CSDL bằng PDO
$host = "localhost";
$dbname = "sportshop";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}

// 2. Kiểm tra đăng nhập
if (!isset($_SESSION['customer']['id'])) {
    header("Location: login.php");
    exit;
}
$customer_id = $_SESSION['customer']['id'];

// 3. Kiểm tra kết quả thanh toán MoMo
if (isset($_GET['resultCode']) && $_GET['resultCode'] == '0') {
    // Dữ liệu từ session
    $delivery_address = $_SESSION['momo_delivery_address'] ?? '';
    $shipping_fee = $_SESSION['momo_shipping_fee'] ?? 0;
    $total_amount = $_SESSION['momo_total_amount'] ?? 0;
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        $_SESSION['error_message'] = "❌ Giỏ hàng rỗng, không thể tạo đơn.";
        header("Location: cart.php");
        exit;
    }

    try {
        $conn->beginTransaction();

        // 4. Tạo đơn hàng
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, order_date, delivery_address, payment_method, shipping_fee, total_amount, status)
                                VALUES (?, NOW(), ?, 'MoMo', ?, ?, 'Đã thanh toán')");
        $stmt->execute([$customer_id, $delivery_address, $shipping_fee, $total_amount]);
        $order_id = $conn->lastInsertId();

        // 5. Thêm sản phẩm vào order_items và trừ tồn kho ở bảng product_sizes theo size
        $insertItemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, size, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $updateQtyStmt = $conn->prepare("UPDATE product_sizes SET quantity = quantity - ? 
                                         WHERE product_id = ? AND size = ? AND quantity >= ?");

        foreach ($cart as $item) {
            $product_id = $item['id'];
            $size = $item['size'] ?? '';  // đảm bảo có size
            $qty = $item['quantity'];
            $price = $item['price'];

            // Trừ tồn kho
            $updateQtyStmt->execute([$qty, $product_id, $size, $qty]);
            if ($updateQtyStmt->rowCount() == 0) {
                $conn->rollBack();
                $_SESSION['error_message'] = "❌ Sản phẩm '{$item['name']}' size '$size' không đủ hàng trong kho.";
                header("Location: orders.php");
                exit;
            }

            // Lưu vào order_items
            $insertItemStmt->execute([$order_id, $product_id, $size, $qty, $price]);
        }

        // 6. Xóa giỏ hàng khỏi session
        unset($_SESSION['cart']);

        // 7. Commit
        $conn->commit();
        $_SESSION['success_message'] = "🎉 Thanh toán thành công qua MoMo! Đơn hàng #$order_id đã được ghi nhận.";

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "❌ Có lỗi xảy ra khi xử lý đơn hàng: " . $e->getMessage();
    }

} else {
    $_SESSION['error_message'] = "❌ Thanh toán qua MoMo bị huỷ hoặc thất bại.";
}

// 8. Quay lại trang đơn hàng
header("Location: orders.php");
exit;
