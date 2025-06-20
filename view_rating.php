<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$product_id = $_GET['product_id'] ?? null;

if (!$product_id) {
    echo "Thiếu mã sản phẩm.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM product_ratings WHERE customer_id = ? AND product_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("ii", $customer_id, $product_id);
$stmt->execute();
$rating = $stmt->get_result()->fetch_assoc();

if (!$rating) {
    echo "Không tìm thấy đánh giá.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_rating = (int) $_POST['rating'];
    $new_comment = trim($_POST['review']);

    // Cập nhật đánh giá
    $update_stmt = $conn->prepare("UPDATE product_ratings SET rating = ?, review = ?, updated = 1 WHERE id = ?");
    $update_stmt->bind_param("isi", $new_rating, $new_comment, $rating['id']);
    $update_stmt->execute();
    $update_stmt->close();

    // Tính lại trung bình và số lượt đánh giá
    $update_avg = $conn->prepare("
        UPDATE products
        SET rating = (
            SELECT ROUND(AVG(rating), 1) FROM product_ratings WHERE product_id = ?
        ),
        rating_count = (
            SELECT COUNT(*) FROM product_ratings WHERE product_id = ?
        )
        WHERE id = ?
    ");
    $update_avg->bind_param("iii", $product_id, $product_id, $product_id);
    $update_avg->execute();
    $update_avg->close();

    header("Location: my_ratings.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-3">
            <?php include 'includes/account_sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="bg-white p-4 rounded shadow-sm">
                <h2>⭐ Cập nhật đánh giá</h2>
                <form method="POST">
                    <div class="mb-3">
                        <label for="rating" class="form-label fw-bold">Điểm đánh giá:</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == $rating['rating'] ? 'selected' : '' ?>><?= $i ?> ⭐</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="review" class="form-label fw-bold">Bình luận:</label>
                        <textarea name="review" id="review" class="form-control" rows="5" required><?= htmlspecialchars($rating['review']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-warning">✔️ Cập nhật</button>
                    <a href="my_ratings.php" class="btn btn-secondary ms-2">↩️ Quay lại</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
