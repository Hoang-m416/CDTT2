<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// Xử lý xóa khách hàng
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM customers WHERE id = $delete_id");
    header("Location: manage_customers.php");
    exit();
}

$result = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý khách hàng - SportShop</title>
    <link rel="stylesheet" href="admin_style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f8f9fa;
            color: #333;
        }

        .main {
            margin-left: 220px;
            padding: 30px;
            background: #fff;
            min-height: 100vh;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .table th, .table td {
            vertical-align: middle !important;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 220px;
            background: #343a40;
            padding-top: 60px;
        }

        .sidebar a {
            color: #fff;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
        }

        .btn-danger {
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Nội dung chính -->
    <div class="main">
        <h1>👤 Quản lý khách hàng</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Địa chỉ</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td class="text-center"><?= $row['created_at'] ?? '-' ?></td>
                        <td class="text-center">
                            <a href="manage_customers.php?delete_id=<?= $row['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
