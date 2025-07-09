<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$admin_id = $_SESSION['admin_id'];
$customer_id = $_GET['cust_id'] ?? 0;


// ✅ Đánh dấu tất cả tin nhắn của khách này là đã đọc
$conn->query("UPDATE messages SET is_read = 1 
              WHERE sender = 'customer' AND sender_id = $customer_id AND receiver_id = $admin_id AND is_read = 0");

// Gửi tin nhắn
if (isset($_POST['send']) && !empty($_POST['message'])) {
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO messages (sender, sender_id, receiver_id, message, is_read, created_at) VALUES ('admin', ?, ?, ?, 0, NOW())");
    $stmt->bind_param("iis", $admin_id, $customer_id, $message);
    $stmt->execute();
}

// Lấy toàn bộ tin nhắn giữa admin và khách hàng
$stmt = $conn->prepare("
    SELECT * FROM messages
    WHERE 
        (sender_id = ? AND receiver_id = ? AND sender = 'customer')
        OR 
        (sender_id = ? AND receiver_id = ? AND sender = 'admin')
    ORDER BY created_at ASC
");
$stmt->bind_param("iiii", $customer_id, $admin_id, $admin_id, $customer_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trò chuyện với khách hàng</title>
    <link rel="stylesheet" href="admin_style.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f2f6;
            margin: 0;
        }

        .main {
            margin-left: 260px;
            padding: 40px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            max-width: 800px;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .chat-header {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .chat-box {
            height: 400px;
            overflow-y: auto;
            padding-right: 10px;
            margin-bottom: 20px;
        }

        .message-row {
            display: flex;
            margin-bottom: 15px;
        }

        .message-row.admin {
            justify-content: flex-end;
        }

        .message-row.customer {
            justify-content: flex-start;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #666;
            margin: 0 8px;
            flex-shrink: 0;
        }

        .bubble {
            max-width: 65%;
            padding: 12px 15px;
            border-radius: 16px;
            font-size: 15px;
            line-height: 1.4;
            position: relative;
        }

        .admin .bubble {
            background-color: #007bff;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .customer .bubble {
            background-color: #f1f0f0;
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .timestamp {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
            text-align: right;
        }

        form textarea {
            width: 100%;
            height: 80px;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 10px;
            resize: none;
        }

        button {
            margin-top: 10px;
            padding: 10px 24px;
            background-color: #ee4d2d;
            border: none;
            color: white;
            font-size: 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #d8431c;
        }

        .back-link {
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 20px;
            }
            .chat-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <div class="chat-container">
        <div class="chat-header">💬 Trò chuyện với khách hàng #<?= $customer_id ?></div>

        <div class="chat-box" id="chatBox">
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <?php
                    $isAdmin = $msg['sender'] === 'admin';
                    $class = $isAdmin ? 'admin' : 'customer';
                    $avatar = $isAdmin ? 'A' : 'K';
                ?>
                <div class="message-row <?= $class ?>">
                    <?php if (!$isAdmin): ?>
                        <div class="avatar"><?= $avatar ?></div>
                    <?php endif; ?>
                    <div>
                        <div class="bubble <?= $class ?>">
                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        </div>
                        <div class="timestamp"><?= $msg['created_at'] ?></div>
                    </div>
                    <?php if ($isAdmin): ?>
                        <div class="avatar"><?= $avatar ?></div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <form method="post">
            <textarea name="message" placeholder="Nhập tin nhắn..." required></textarea>
            <button type="submit" name="send">Gửi</button>
        </form>

        <div class="back-link">
            <p><a href="admin_messages.php">← Quay lại danh sách khách hàng</a></p>
        </div>
    </div>
</div>

<script>
    // Tự động cuộn xuống cuối chat
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>
