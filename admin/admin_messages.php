<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// Lấy danh sách khách hàng đã từng nhắn tin
$result = $conn->query("SELECT DISTINCT sender_id FROM messages WHERE sender = 'customer'");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tin nhắn khách hàng</title>
    <link rel="stylesheet" href="admin_style.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding: 20px;
        }

        .main {
            margin-left: 260px;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            min-height: 100vh;
        }

        h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .customer-list {
            margin-top: 20px;
        }

        .customer-list a {
            display: block;
            background-color: #ffffff;
            border-left: 5px solid #0d6efd;
            padding: 15px 20px;
            margin-bottom: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            color: #0d6efd;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .customer-list a:hover {
            transform: translateY(-3px);
            background-color: #f1f8ff;
            text-decoration: none;
        }

        .back-link {
            margin-top: 30px;
            display: inline-block;
            color: #555;
            text-decoration: none;
            font-size: 15px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <h2>📩 Danh sách khách hàng đã nhắn tin</h2>

    <div class="customer-list">
        <?php while ($row = $result->fetch_assoc()):
            $cust_id = $row['sender_id'];
        ?>
            <a href="chat_with_customer.php?cust_id=<?= $cust_id ?>">Chat với khách hàng #<?= $cust_id ?></a>
        <?php endwhile; ?>
    </div>

</div>

</body>
</html>
