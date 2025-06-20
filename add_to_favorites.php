<?php
session_start();

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Nếu đã đăng nhập
    if (isset($_SESSION['customer'])) {
        $customerId = $_SESSION['customer']['id'];

        // Kiểm tra xem sản phẩm đã được yêu thích chưa
        $check = $conn->prepare("SELECT id FROM favorites WHERE customer_id = ? AND product_id = ?");
        if (!$check) {
            echo "error_prepare";
            exit;
        }
        $check->bind_param("ii", $customerId, $productId);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $createdAt = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO favorites (customer_id, product_id, created_at) VALUES (?, ?, ?)");
            if (!$stmt) {
                echo "error_prepare_insert";
                exit;
            }
            $stmt->bind_param("iis", $customerId, $productId, $createdAt);
            echo $stmt->execute() ? "added" : "error_execute";
            $stmt->close();
        } else {
            echo "exists";
        }
        $check->close();
    } 
    // Nếu chưa đăng nhập, lưu trong session
    else {
        if (!isset($_SESSION['favorites']) || !is_array($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }

        if (!in_array($productId, $_SESSION['favorites'])) {
            $_SESSION['favorites'][] = $productId;
            echo "added_session";
        } else {
            echo "exists_session";
        }
    }
} else {
    echo "error_request";
}

$conn->close();
?>
