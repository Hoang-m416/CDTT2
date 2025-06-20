<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['customer']['id'];

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// Lấy các đánh giá của người dùng
$stmt = $conn->prepare("
    SELECT pr.*, p.name AS product_name, p.image 
    FROM product_ratings pr 
    JOIN products p ON pr.product_id = p.id 
    WHERE pr.customer_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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
                <h2>⭐ Đánh giá của tôi</h2>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="border rounded p-3 mb-4 d-flex align-items-start gap-3 shadow-sm">
                            <img src="admin/uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" width="100" class="rounded border">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($row['product_name']) ?></h5>
                                <p class="mb-1">⭐ <strong><?= $row['rating'] ?>/5</strong></p>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($row['review'])) ?></p>
                                <small class="text-muted">Đánh giá lúc: <?= $row['created_at'] ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Bạn chưa có đánh giá nào.</p>
                <?php endif; ?>

                <a href="profile.php" class="btn btn-warning mt-3">← Quay lại hồ sơ</a>
            </div>
        </div>
    </div>
</div>

<?php include 'chatbox.php'; ?>
<?php include 'includes/footer.php'; ?>

<!-- Custom style đồng bộ -->
<style>
body {
    background: linear-gradient(135deg, #f3f4f6, #ffffff);
    font-family: 'Segoe UI', sans-serif;
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
}

.btn-warning:hover {
    background-color: #e0a800;
    color: #fff;
}

.card-title, h5 {
    font-weight: 600;
}
</style>
