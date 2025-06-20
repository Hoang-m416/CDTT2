<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$user = $_SESSION['customer'];
$user_id = $user['id'];
$message = "";
$pass_message = "";

// Lấy danh sách tỉnh
$provinces = [];
$res = $conn->query("SELECT DISTINCT province FROM shipping_fee");
while ($row = $res->fetch_assoc()) {
    $provinces[] = $row['province'];
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $province = trim($_POST['province']);

    // Xử lý upload ảnh nếu có
    $avatar_filename = $user['avatar']; // giữ nguyên nếu không upload mới
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $avatar_filename = "avatar_" . $user_id . "_" . time() . "." . $ext;
        $target_dir = __DIR__ . "/assets/images/users/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_path = $target_dir . $avatar_filename;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path);
    }

    // Cập nhật CSDL
    $stmt = $conn->prepare("UPDATE customers SET full_name=?, email=?, phone=?, address=?, province=?, avatar=? WHERE id=?");
    $stmt->bind_param("ssssssi", $full_name, $email, $phone, $address, $province, $avatar_filename, $user_id);
    $stmt->execute();

    // Cập nhật session
    $res = $conn->query("SELECT * FROM customers WHERE id = $user_id");
    $_SESSION['customer'] = $res->fetch_assoc();
    $user = $_SESSION['customer'];
    $message = "✅ Cập nhật thông tin thành công!";
}

// Đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $res = $conn->query("SELECT password FROM customers WHERE id = $user_id");
    $row = $res->fetch_assoc();

    if ($row && password_verify($current_password, $row['password'])) {
        if ($new_password === $confirm_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE customers SET password=? WHERE id=?");
            $stmt->bind_param("si", $hashed, $user_id);
            $stmt->execute();
            $pass_message = "✅ Đổi mật khẩu thành công!";
        } else {
            $pass_message = "❌ Mật khẩu mới không khớp.";
        }
    } else {
        $pass_message = "❌ Mật khẩu hiện tại không đúng.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5 mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'includes/account_sidebar.php'; ?>
        </div>

        <!-- Nội dung -->
        <div class="col-md-9">
            <div class="bg-white p-4 rounded shadow-sm">
                <h2 class="mb-4">✏️ Cập nhật thông tin cá nhân</h2>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_info" value="1">

                    <div class="mb-3">
                        <label>Ảnh đại diện hiện tại:</label><br>
                        <img src="assets/images/users/<?= htmlspecialchars($user['avatar']) ?>" width="100" height="100" class="rounded-circle" alt="avatar">
                    </div>

                    <div class="mb-3">
                        <label>Đổi ảnh đại diện:</label>
                        <input type="file" name="avatar" accept="image/*" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Họ tên:</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Số điện thoại:</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Địa chỉ:</label>
                        <textarea name="address" class="form-control" required><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Tỉnh/Thành:</label>
                        <select name="province" class="form-select" required>
                            <option value="">-- Chọn tỉnh/thành --</option>
                            <?php foreach ($provinces as $prov): ?>
                                <option value="<?= htmlspecialchars($prov) ?>" <?= $user['province'] === $prov ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($prov) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
                    <a href="profile.php" class="btn btn-secondary">← Quay lại</a>
                </form>
            </div>

            <!-- Đổi mật khẩu -->
            <div class="bg-white p-4 rounded shadow-sm mt-5">
                <h2 class="mb-4">🔒 Đổi mật khẩu</h2>

                <?php if ($pass_message): ?>
                    <div class="alert alert-info"><?= $pass_message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="change_password" value="1">

                    <div class="mb-3">
                        <label>Mật khẩu hiện tại:</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                            <span class="input-group-text" id="check_icon"><i class="text-muted">⏳</i></span>
                        </div>
                        <small id="password_feedback" class="text-muted"></small>
                    </div>
                    <div class="mb-3">
                        <label>Mật khẩu mới:</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Xác nhận mật khẩu mới:</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">🔁 Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'chatbox.php'; ?>

<?php include 'includes/footer.php'; ?>
