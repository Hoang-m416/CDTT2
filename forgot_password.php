<?php
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE username = ? AND email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE customers SET password = ? WHERE username = ?");
            $update->bind_param("ss", $hashed, $username);
            $update->execute();
            $success = "✅ Mật khẩu đã được đặt lại. Bạn có thể <a href='login.php'>đăng nhập</a> lại.";
        } else {
            $error = "Tên đăng nhập hoặc email không đúng.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quên mật khẩu - SportShop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .reset-wrapper {
      max-width: 450px;
      margin: 60px auto;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    h3 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
      color: #333;
    }
    .form-control {
      border-radius: 10px;
    }
    .btn-primary {
      border-radius: 10px;
      font-weight: 600;
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container">
  <div class="reset-wrapper">
    <h3>Quên mật khẩu</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email đăng ký" required>
      </div>
      <div class="mb-3">
        <input type="password" name="new_password" class="form-control" placeholder="Mật khẩu mới" required>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
      </div>
      <div class="d-flex justify-content-between">
  <a href="login.php" class="btn btn-outline-secondary">← Quay lại</a>
  <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
</div>

    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
