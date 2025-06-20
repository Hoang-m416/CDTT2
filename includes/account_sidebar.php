<aside class="account-sidebar">
    <h3>Tài khoản</h3>
    <ul>
        <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">👤 Hồ sơ</a></li>
        <li><a href="update_profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'update_profile.php' ? 'active' : '' ?>">✏️ Cập nhật</a></li>
        <li><a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">📦 Đơn hàng</a></li>
        <li><a href="my_ratings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'my_ratings.php' ? 'active' : '' ?>">⭐ Đánh giá của tôi</a></li>
        <li><a href="logout.php" class="text-danger">🚪 Đăng xuất</a></li>
    </ul>
</aside>
<style>
    .account-wrapper {
    display: flex;
    min-height: 100vh;
    background: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
}

.account-sidebar {
    width: 240px;
    background-color: #fff;
    padding: 30px 20px;
    box-shadow: 2px 0 8px rgba(0,0,0,0.05);
    position: sticky;
    top: 0;
    height: 100vh;
}

.account-sidebar h3 {
    font-size: 20px;
    margin-bottom: 24px;
    color: #007bff;
}

.account-sidebar ul {
    list-style: none;
    padding-left: 0;
}

.account-sidebar ul li {
    margin-bottom: 14px;
}

.account-sidebar ul li a {
    text-decoration: none;
    color: #333;
    font-size: 16px;
    display: block;
    padding: 8px 12px;
    border-radius: 8px;
    transition: 0.3s;
}

.account-sidebar ul li a:hover,
.account-sidebar ul li a.active {
    background-color: #007bff;
    color: #fff;
}

.account-content {
    flex: 1;
    padding: 40px;
    background: #f1f3f6;
}
</style>