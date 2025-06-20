<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); // Có thể là tên đăng nhập hoặc email
    $password = $_POST['password'];

    // Truy vấn theo username hoặc email
    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username); // Dùng cùng một biến cho cả username và email
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Gán session đăng nhập
        $_SESSION['customer'] = $user;
        $_SESSION['customer_id'] = $user['id'];

        // Khởi tạo giỏ hàng từ DB
        $_SESSION['cart'] = [];
        $customer_id = $user['id'];

        $stmt = $conn->prepare("SELECT ci.product_id, ci.size, ci.quantity, p.name, p.image, p.price 
                                FROM cart_items ci 
                                JOIN products p ON ci.product_id = p.id 
                                WHERE ci.customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $key = $row['product_id'] . '_' . $row['size'];
            $_SESSION['cart'][$key] = [
                'id'        => $row['product_id'],
                'product_id'=> $row['product_id'],
                'name'      => $row['name'],
                'image'     => $row['image'],
                'size'      => $row['size'],
                'price'     => $row['price'],
                'quantity'  => $row['quantity']
            ];
        }

        header("Location: index.php");
        exit;
    } else {
        $error = "Sai tên đăng nhập/email hoặc mật khẩu.";
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập - SportShop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    body {
      background: linear-gradient(to right, #fefefe, #f0f0f0);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-wrapper {
      max-width: 400px;
      margin: 60px auto;
      padding: 40px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      font-weight: 600;
      color: #333;
      margin-bottom: 30px;
    }

    .form-control {
      height: 48px;
      border-radius: 10px;
      font-size: 16px;
      border: 1px solid #ddd;
      padding: 10px 15px;
    }

    .form-control:focus {
      border-color: #3a86ff;
      box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.2);
    }

    .btn-success {
      width: 100%;
      background: #3a86ff;
      border: none;
      font-weight: 600;
      border-radius: 10px;
      height: 45px;
      transition: 0.3s;
    }

    .btn-success:hover {
      background: #265dc7;
    }

    .btn-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #3a86ff;
      font-weight: 500;
      text-decoration: none;
    }

    .btn-link:hover {
      text-decoration: underline;
    }

    .alert {
      border-radius: 10px;
    }

    @media (max-width: 576px) {
      .login-wrapper {
        padding: 30px 20px;
        margin: 40px 15px;
      }
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container">
  <div class="login-wrapper">
    <h2>Đăng nhập</h2>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập/Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
      </div>
      <button type="submit" class="btn btn-success">Đăng nhập</button>
      <a href="register.php" class="btn-link">Chưa có tài khoản? Đăng ký ngay</a>
      <a href="forgot_password.php" class="btn btn-link">Quên mật khẩu?</a>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
