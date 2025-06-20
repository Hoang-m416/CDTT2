<?php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>

<div class="sidebar">
    <h2>SportShop</h2>

    <div class="admin-info">
        <p>Xin chào, <strong><?= htmlspecialchars($adminName) ?></strong></p>
        <a href="admin_logout.php" class="logout-btn">Đăng xuất</a>
    </div>
    <a href="dashboard.php" <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'class="active"' : '' ?>>Dashboard</a>
    <a href="admin_orders.php" <?= basename($_SERVER['PHP_SELF']) === 'admin_orders.php' ? 'class="active"' : '' ?>>Quản lý đơn hàng</a>
    <a href="admin_messages.php" <?= basename($_SERVER['PHP_SELF']) === 'admin_messages.php' ? 'class="active"' : '' ?>>Quản lý tin nhắn</a>
    <a href="admin_add_product.php" <?= basename($_SERVER['PHP_SELF']) === 'admin_add_product.php' ? 'class="active"' : '' ?>>Thêm sản phẩm</a>
    <a href="products_list.php" <?= basename($_SERVER['PHP_SELF']) === 'products_list.php' ? 'class="active"' : '' ?>>Danh sách sản phẩm</a>
    <a href="admin_category_brand.php" <?= basename($_SERVER['PHP_SELF']) === 'admin_category_brand.php' ? 'class="active"' : '' ?>>Danh mục và Thương hiệu</a>
    <a href="admin_shipping_fee.php" <?= basename($_SERVER['PHP_SELF']) === 'admin_shipping_fee.php' ? 'class="active"' : '' ?>>Phí vận chuyển</a>
    <a href="manage_customers.php" <?= basename($_SERVER['PHP_SELF']) === 'manage_customers.php' ? 'class="active"' : '' ?>>Quản lý khách hàng</a>
    <a href="admin_reviews.php" <?= basename($_SERVER['PHP_SELF']) === 'admin_reviews.php' ? 'class="active"' : '' ?>>Quản lý đánh giá</a>
    <a href="update_admin.php" <?= basename($_SERVER['PHP_SELF']) === 'update_admin.php' ? 'class="active"' : '' ?>>Cập nhật tài khoản</a>

</div>
<style>
    .sidebar {
    width: 250px;
    height: 100vh; /* Chiều cao toàn màn hình */
    overflow-y: auto; /* Cho phép cuộn khi nội dung dài */
    position: fixed;
    top: 0;
    left: 0;
    background-color:rgb(74, 25, 181);
    color: white;
    padding: 20px;
    box-sizing: border-box;
}

.sidebar a {
    display: block;
    padding: 10px;
    color: white;
    text-decoration: none;
    margin-bottom: 5px;
}

.sidebar a.active {
    background-color: #1abc9c;
    font-weight: bold;
}

.main {
    margin-left: 270px; /* Nhường chỗ cho sidebar */
    padding: 20px;
}
</style>
