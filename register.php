<?php
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$errors = [];
$success = false;

// Khởi tạo các biến trống để giữ dữ liệu
$username = $full_name = $email = $phone = $address = $province = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password_input = $_POST['password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $province = $_POST['province'];

    // Kiểm tra mật khẩu
    if (strlen($password_input) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }

    // Kiểm tra username hoặc email trùng
    $stmt = $conn->prepare("SELECT id FROM customers WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Tên đăng nhập hoặc Email đã tồn tại.";
    }

    // Nếu không có lỗi → đăng ký
    if (empty($errors)) {
        $password = password_hash($password_input, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO customers (username, full_name, email, password, phone, address, province)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $username, $full_name, $email, $password, $phone, $address, $province);
        $stmt->execute();
        $success = true;

        // Reset lại form
        $username = $full_name = $email = $phone = $address = $province = "";
    }
}

$province_result = $conn->query("SELECT province FROM shipping_fee ORDER BY province ASC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký - SportShop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color:rgb(150, 146, 146);
    }
    .register-wrapper {
      max-width: 800px;
      margin: 50px auto;
      background-color: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 5px 30px rgba(0, 0, 0, 0.05);
    }
    .register-wrapper h2 {
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      color: #333;
    }
    .form-control, .form-select {
      border-radius: 10px;
    }
    .btn-primary {
      padding: 10px 25px;
      border-radius: 10px;
      font-weight: 600;
    }
    .btn-link {
      font-weight: 500;
      text-decoration: none;
    }
    .alert {
      border-radius: 10px;
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
  <div class="register-wrapper">
    <h2>Đăng ký tài khoản</h2>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ Đăng ký thành công!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        ❌ <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
      </div>
    <?php endforeach; ?>

    <form method="POST">
      <div class="row">
        <div class="col-md-6 mb-3">
          <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required value="<?= htmlspecialchars($username) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <input type="text" name="full_name" class="form-control" placeholder="Họ và tên" required value="<?= htmlspecialchars($full_name) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required value="<?= htmlspecialchars($email) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
        </div>
        <div class="col-md-6 mb-3">
          <input type="text" name="phone" class="form-control" placeholder="Số điện thoại" required value="<?= htmlspecialchars($phone) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <select name="province" class="form-select" required>
            <option value="">Chọn tỉnh/thành</option>
            <?php while ($row = $province_result->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($row['province']) ?>" <?= ($province == $row['province']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['province']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-12 mb-3">
          <textarea name="address" class="form-control" placeholder="Địa chỉ cụ thể" rows="3" required><?= htmlspecialchars($address) ?></textarea>
        </div>
        <div class="col-12 d-flex justify-content-between align-items-center mt-3">
          <a href="login.php" class="btn btn-outline-secondary">← Quay lại đăng nhập</a>
          <button type="submit" class="btn btn-primary">Đăng ký</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include 'chatbox.php'; ?>
<?php include 'includes/footer.php'; ?>

</body>
</html>
