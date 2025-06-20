<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// Lấy danh sách đánh giá từ product_ratings
$query = "
    SELECT pr.*, 
           p.name AS product_name, 
           c.full_name AS customer_name, 
           c.id AS customer_id
    FROM product_ratings pr
    LEFT JOIN products p ON pr.product_id = p.id
    LEFT JOIN customers c ON pr.customer_id = c.id
    ORDER BY pr.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đánh giá sản phẩm</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .main {
            margin-left: 260px;
            padding: 40px;
        }

        h1 {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            color: #333;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .btn-chat {
            display: inline-block;
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-chat:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 20px;
            }

            table {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <h1>📋 Danh sách đánh giá sản phẩm</h1>

    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Khách hàng</th>
                <th>Đánh giá</th>
                <th>Số sao</th>
                <th>Ngày</th>
                <th>Trò chuyện</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name'] ?? 'Không xác định') ?></td>
                    <td><?= htmlspecialchars($row['customer_name'] ?? 'Không xác định') ?></td>
                    <td><?= nl2br(htmlspecialchars($row['review'] ?? '')) ?></td>
                    <td><?= (int)$row['rating'] ?> ⭐</td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <?php if (!empty($row['customer_id'])): ?>
                            <a class="btn-chat" href="chat_with_customer.php?cust_id=<?= $row['customer_id'] ?>">
                                💬 Chat
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
