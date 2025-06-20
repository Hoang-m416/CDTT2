<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location:  admin_login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Xóa size trước (nếu không có ON DELETE CASCADE)
    $conn->query("DELETE FROM product_sizes WHERE product_id = $id");

    // Xóa sản phẩm
    $conn->query("DELETE FROM products WHERE id = $id");

    header("Location: products_list.php");
    exit;
} else {
    echo "Không có ID sản phẩm để xóa.";
}
?>
