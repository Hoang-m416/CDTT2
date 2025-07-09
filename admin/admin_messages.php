<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// ✅ Xử lý khi admin nhấn "Ẩn" khách hàng
if (isset($_POST['hide_customer_id'])) {
    $hide_id = (int)$_POST['hide_customer_id'];

    // Ẩn tin nhắn mới nhất của khách hàng
    $res = $conn->query("
        SELECT id FROM messages 
        WHERE sender = 'customer' AND sender_id = $hide_id 
        ORDER BY created_at DESC LIMIT 1
    ");
    if ($row = $res->fetch_assoc()) {
        $msg_id = $row['id'];
        $conn->query("UPDATE messages SET hidden_by_admin = 1 WHERE id = $msg_id");
    }
}

// ✅ Lấy danh sách khách hàng có tin nhắn mới nhất chưa bị ẩn
$sql = "
    SELECT m.sender_id, 
           COUNT(CASE WHEN m.is_read = 0 THEN 1 END) AS unread_count
    FROM messages m
    INNER JOIN (
        SELECT sender_id, MAX(created_at) AS latest
        FROM messages
        WHERE sender = 'customer'
        GROUP BY sender_id
    ) latest_msg ON m.sender_id = latest_msg.sender_id AND m.created_at = latest_msg.latest
    WHERE m.sender = 'customer' AND m.hidden_by_admin = 0
    GROUP BY m.sender_id
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hỗ trợ khách hàng</title>
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

        .customer-item {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            border-left: 5px solid #0d6efd;
            padding: 15px 20px;
            margin-bottom: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }

        .customer-item:hover {
            background-color: #f1f8ff;
        }

        .customer-link {
            color: #0d6efd;
            font-weight: 500;
            text-decoration: none;
        }

        .unread-badge {
            background: red;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }

        .hide-btn {
            background: none;
            border: none;
            color: #dc3545;
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .hide-btn:hover {
            background: rgba(220, 53, 69, 0.1); /* nền đỏ nhạt khi hover */
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
            $unread = $row['unread_count'];
        ?>
            <div class="customer-item">
                <a class="customer-link" href="chat_with_customer.php?cust_id=<?= $cust_id ?>">
                    Chat với khách hàng #<?= $cust_id ?>
                    <?php if ($unread > 0): ?>
                        <span class="unread-badge"><?= $unread ?> mới</span>
                    <?php endif; ?>
                </a>
                <form method="post" style="margin: 0;">
                    <input type="hidden" name="hide_customer_id" value="<?= $cust_id ?>">
                    <button type="submit" name="hide" class="hide-btn" title="Ẩn cuộc trò chuyện">❌</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

</div>

</body>
</html>
