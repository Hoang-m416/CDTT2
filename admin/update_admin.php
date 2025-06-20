<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Lấy thông tin admin hiện tại
$username = $_SESSION['admin_username'];
$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin SET username = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_username, $hashed_password, $admin['id']);
    } else {
        $stmt = $conn->prepare("UPDATE admin SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $admin['id']);
    }

    if ($stmt->execute()) {
        $_SESSION['admin_username'] = $new_username;
        $success = "Cập nhật thành công!";
    } else {
        $error = "Cập nhật thất bại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin Admin</title>
    <link rel="stylesheet" href="admin_style.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .main {
            margin-left: 260px;
            padding: 50px 20px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 20px;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ee4d2d;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        button:hover {
            background-color: #d8431c;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .message {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        @media screen and (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 20px;
            }

            .form-container {
                padding: 20px;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <div class="form-container">
        <h2>Cập nhật tài khoản Admin</h2>

        <?php if (isset($success)) echo "<p class='message success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='message error'>$error</p>"; ?>

        <form method="post" action="">
            <label for="username">Tên đăng nhập mới:</label>
            <input type="text" name="username" id="username"
                   value="<?= htmlspecialchars($admin['username']) ?>" required>

            <label for="password">Mật khẩu mới (để trống nếu không đổi):</label>
            <input type="password" name="password" id="password">

            <button type="submit" name="update">Cập nhật</button>
        </form>

        <div class="back-link">
            <a href="dashboard.php">← Quay lại trang quản trị</a>
        </div>
    </div>
</div>

</body>
</html>
