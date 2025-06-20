<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit;
}
$customer_id = $_SESSION['customer']['id'];
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$product_id = $_GET['product_id'] ?? 0;
$order_id = $_GET['order_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int) $_POST['rating'];
    $review = trim($_POST['review']);

    // Thêm đánh giá mới
    $stmt = $conn->prepare("INSERT INTO product_ratings (customer_id, product_id, order_id, rating, review) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $customer_id, $product_id, $order_id, $rating, $review);
    $stmt->execute();
    $stmt->close();

    // Cập nhật lại rating trung bình và số lượt đánh giá
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

    header("Location: orders.php");
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<div class="container py-5 mt-5">
    <div class="bg-white p-4 rounded shadow-sm">
        <h2>⭐ Đánh giá sản phẩm</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="rating" class="form-label">Số sao (1 đến 5):</label>
                <select name="rating" id="rating" class="form-select" required>
                    <option value="">--Chọn--</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> sao</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="review" class="form-label">Nhận xét:</label>
                <textarea name="review" id="review" rows="4" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
