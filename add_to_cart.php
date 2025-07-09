<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $size = trim($_POST['size'] ?? '');
    $size = htmlspecialchars($size);
    $quantity = (int)$_POST['quantity'];

    if ($product_id <= 0 || !$size || $quantity <= 0) {
        echo "error:Dữ liệu không hợp lệ.";
        exit;
    }

    $conn = new mysqli("localhost", "root", "", "sportshop");
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        echo "error:Kết nối CSDL thất bại.";
        exit;
    }

    // Kiểm tra tồn kho
    $stmt_stock = $conn->prepare("SELECT quantity FROM product_sizes WHERE product_id = ? AND size = ?");
    $stmt_stock->bind_param("is", $product_id, $size);
    $stmt_stock->execute();
    $result_stock = $stmt_stock->get_result();

    if (!$row_stock = $result_stock->fetch_assoc()) {
        // Lấy danh sách size hiện có để debug
        $debug_sizes = [];
        $stmt_sizes = $conn->prepare("SELECT size FROM product_sizes WHERE product_id = ?");
        $stmt_sizes->bind_param("i", $product_id);
        $stmt_sizes->execute();
        $res_sizes = $stmt_sizes->get_result();
        while ($r = $res_sizes->fetch_assoc()) {
            $debug_sizes[] = $r['size'];
        }

        echo "error:Không tìm thấy tồn kho cho size \"$size\". Các size có sẵn: " . implode(', ', $debug_sizes);
        exit;
    }

    $stock_quantity = (int)$row_stock['quantity'];
    $cart_key = $product_id . '_' . $size;
    $session_qty = $_SESSION['cart'][$cart_key]['quantity'] ?? 0;

    $customer_id = $_SESSION['customer']['id'] ?? null;
    $db_qty = 0;

    if ($customer_id) {
        $stmt_db = $conn->prepare("SELECT quantity FROM cart_items WHERE customer_id = ? AND product_id = ? AND size = ?");
        $stmt_db->bind_param("iis", $customer_id, $product_id, $size);
        $stmt_db->execute();
        $res_db = $stmt_db->get_result();
        if ($row_db = $res_db->fetch_assoc()) {
            $db_qty = (int)$row_db['quantity'];
        }
    }

    // ✅ Chỉ xét 1 nơi (session hoặc DB)
    $current_qty = $customer_id ? $db_qty : $session_qty;
    $total_after_add = $current_qty + $quantity;

    if ($total_after_add > $stock_quantity) {
        $available_qty = max(0, $stock_quantity - $current_qty);

        if ($available_qty <= 0) {
            echo "error:Sản phẩm size \"$size\" đã hết hàng hoặc bạn đã thêm hết số lượng vào giỏ.";
        } else {
            echo "error:Chỉ có thể thêm tối đa $available_qty sản phẩm size \"$size\" vào giỏ (còn lại trong kho).";
        }
        exit;
    }

    // Lấy thông tin sản phẩm
    $stmt = $conn->prepare("SELECT id, name, image, price, discount_percentage FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "error:Sản phẩm không tồn tại.";
        exit;
    }

    $price_after_discount = $product['price'] * (1 - $product['discount_percentage'] / 100);

    // Thêm vào session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'image' => $product['image'],
            'price' => $price_after_discount,
            'size' => $size,
            'quantity' => $quantity
        ];
    }

    // Nếu người dùng đã đăng nhập thì lưu DB
    if ($customer_id) {
        $check_stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE customer_id = ? AND product_id = ? AND size = ?");
        $check_stmt->bind_param("iis", $customer_id, $product_id, $size);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($row = $check_result->fetch_assoc()) {
            $new_quantity = $row['quantity'] + $quantity;
            $update_stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $new_quantity, $row['id']);
            $update_stmt->execute();
        } else {
            $created_at = date('Y-m-d H:i:s');
            $insert_stmt = $conn->prepare("INSERT INTO cart_items (customer_id, product_id, size, quantity, price, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("iisids", $customer_id, $product_id, $size, $quantity, $price_after_discount, $created_at);
            $insert_stmt->execute();
        }
    }

    echo "success:Đã thêm $quantity sản phẩm size \"$size\" vào giỏ hàng!";
    exit;
}
?>
