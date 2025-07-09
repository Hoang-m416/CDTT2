<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tính tổng số lượng sản phẩm trong giỏ hàng
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu | SportShop</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons for icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-toggle {
            background: none;
            border: none;
            font-weight: 500;
            cursor: pointer;
            color: #333;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .user-toggle:hover {
            background-color: #f0f0f0;
        }

        .user-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 120%;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            min-width: 180px;
            z-index: 1000;
            overflow: hidden;
        }

        .user-menu a {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            color: #333;
            text-decoration: none;
            transition: background 0.2s;
        }

        .user-menu a:hover {
            background-color: #f5f5f5;
        }

        .user-menu i {
            margin-right: 8px;
        }

        .btn-cart {
            position: relative;
        }

        .btn-cart .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
<header>
    <!-- Logo & Menu -->
    <div class="header-left">
        <a href="index.php" class="logo">
            <img src="assets/images/logos/logo1.png" alt="Logo">
        </a>
    </div>

    <!-- Search -->
    <form class="search-bar" action="search.php" method="get">
        <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm ở đây..." />
        <button type="submit"><i class='bx bx-search' id="search-icon"></i></button>
    </form>

    <!-- Right icons -->
    <div class="header-icon">
        <?php if (isset($_SESSION['customer'])): ?>
            <!-- Xin chào + Dropdown -->
            <div class="user-dropdown">
                <button class="user-toggle" onclick="toggleUserMenu()">
                    👋 Xin chào, <?= htmlspecialchars($_SESSION['customer']['full_name']) ?> <i class='bx bx-chevron-down'></i>
                </button>
                <div class="user-menu" id="userMenu">
                    <a href="profile.php"><i class='bx bx-user-circle'></i> Tài khoản</a>
                    <a href="orders.php"><i class='bx bx-receipt'></i> Đơn hàng của tôi</a>
                    <a href="logout.php" class="text-danger"><i class='bx bx-log-out'></i> Đăng Xuất</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Khi chưa đăng nhập -->
            <div class="icon-button user-auth">
                <i class='bx bx-user'></i>
                <div class="auth-links">
                    <a href="login.php" class="login-link">Đăng Nhập</a>
                    <a href="register.php" class="register-link">Đăng Ký</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Các icon khác -->
        <a href="favorites-list.php"><i class='bx bx-heart'></i></a>
        <a href="user_gift.php" class="btn-cart" title="Quà tặng"><i class='bx bx-gift'></i></a>
        <a href="cart.php" class="btn-cart" title="Xem giỏ hàng">
            <i class='bx bx-cart'></i>
            <?php if ($cart_count > 0): ?>
                <span class="badge"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
    </div>
</header>

<!-- Sub menu dưới -->
<nav class="sub-nav">
    <a href="index.php">TRANG CHỦ</a>
    <a href="about.php">GIỚI THIỆU</a>
    <a href="products.php">SẢN PHẨM</a>
    <a href="gift.php"><i class='bx bx-gift'></i> QÙA TẶNG</a>
    <a href="contact.php">LIÊN HỆ</a>
</nav>
<div class="menu-overlay" id="menuOverlay"></div>

<!-- Script dropdown user -->
<script>
function toggleUserMenu() {
    const menu = document.getElementById("userMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

// Ẩn menu khi click ra ngoài
document.addEventListener("click", function(e) {
    const menu = document.getElementById("userMenu");
    const toggle = document.querySelector(".user-toggle");

    if (!menu.contains(e.target) && !toggle.contains(e.target)) {
        menu.style.display = "none";
    }
});
</script>
</body>
</html>
