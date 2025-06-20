<?php
session_start();

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xóa yêu thích trong database (đã đăng nhập)
    if (!empty($_POST['favorite_id'])) {
        $fav_id = intval($_POST['favorite_id']);
        if ($fav_id > 0) {
            $stmt = $conn->prepare("DELETE FROM favorites WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $fav_id);
                if ($stmt->execute()) {
                    echo "deleted";
                } else {
                    echo "error";
                }
                $stmt->close();
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }
    // Xóa yêu thích trong session (chưa đăng nhập)
    else if (!empty($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        if (isset($_SESSION['favorites']) && is_array($_SESSION['favorites'])) {
            $key = array_search($productId, $_SESSION['favorites']);
            if ($key !== false) {
                unset($_SESSION['favorites'][$key]);
                // Sắp xếp lại mảng session sau khi unset
                $_SESSION['favorites'] = array_values($_SESSION['favorites']);
                echo "deleted";
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }
    else {
        echo "error";
    }
} else {
    echo "error";
}

$conn->close();
?>
