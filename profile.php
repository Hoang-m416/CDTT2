<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['customer']['id'];

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<p>Không tìm thấy thông tin người dùng.</p>";
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5 mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'includes/account_sidebar.php'; ?>
        </div>

        <!-- Main content -->
        <div class="col-md-9">
            <div class="bg-white p-4 rounded shadow-sm">
                <h2>👤 Thông tin cá nhân</h2>
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($user['address']) ?></p>
                <p><strong>Tỉnh/Thành:</strong> <?= htmlspecialchars($user['province']) ?></p>
                <a href="update_profile.php" class="btn btn-warning mt-3">✏️ Cập nhật thông tin</a>
            </div>
        </div>
    </div>
</div>
<?php include 'chatbox.php'; ?>

<?php include 'includes/footer.php'; ?>

<!-- Custom style -->
<style>
body {
    background: linear-gradient(135deg, #f3f4f6, #ffffff);
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    color: #333;
}

h2 {
    font-size: 26px;
    font-weight: 600;
    color: #2c3e50;
}

p strong {
    min-width: 160px;
    display: inline-block;
    color: #222;
}

.btn-warning {
    background-color: #ffc107;
    border: none;
    color: #212529;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
}

.btn-warning:hover {
    background-color: #e0a800;
    color: #fff;
}
</style>
