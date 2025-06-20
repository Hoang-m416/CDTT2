<?php
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 3; // số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;

$total_pages = 1;
$result = null;

if (!empty($keyword)) {
    // Đếm tổng số kết quả
    $countSql = "
        SELECT COUNT(*) as total
        FROM products p
        WHERE p.name LIKE ?
    ";
    $stmt = $conn->prepare($countSql);
    $searchTerm = $keyword . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $countResult = $stmt->get_result()->fetch_assoc();
    $total_rows = $countResult['total'];
    $total_pages = ceil($total_rows / $limit);

    // Truy vấn dữ liệu có phân trang
    $sql = "
        SELECT 
            p.id, p.name, p.description, p.price, p.discount_percentage, p.image, 
            p.rating, p.rating_count, c.name AS category_name, b.name AS brand_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN brands b ON p.brand_id = b.id
        WHERE p.name LIKE ?
        ORDER BY p.id DESC
        LIMIT ?, ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchTerm, $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm: <?= htmlspecialchars($keyword) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!--boxicon-->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Thêm vào trong <head> -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
/* Style cho phần tiêu đề */
main h2 {
    font-size: 1.5rem;
    font-weight: bold;
    margin-top: 90px;
    text-align: center;
    color: #333;
}

/* Style cho sản phẩm */
.product-item {
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 15px;
    background-color: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: left; /* Căn trái toàn bộ nội dung */
    position: relative;
}

.product-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.product-item img.product-image {
    width: 100%;
    height: 180px;
    object-fit: contain;
    border-radius: 5px;
    margin-bottom: 10px;
}

.product-item h5 {
    font-size: 1rem;
    margin: 0 0 5px 0;
    color: #222;
}

.product-item .price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1rem;
}

.product-item .old-price {
    text-decoration: line-through;
    color: #999;
    font-size: 0.9rem;
    margin-left: 8px;
}

.product-item .rating-stars {
    font-size: 0.9rem;
    color: #f39c12;
    margin: 6px 0;
}

.product-item .discount-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: #e74c3c;
    color: #fff;
    font-size: 0.8rem;
    padding: 4px 6px;
    border-radius: 5px;
}

/* Button xem chi tiết */
.product-item a.btn {
    margin-top: 8px;
    align-self: flex-start; /* Để nút không căn giữa */
}

/* Responsive thu nhỏ khi mobile */
@media (max-width: 576px) {
    .product-item img.product-image {
        height: 140px;
    }

    .product-item h5 {
        font-size: 0.95rem;
    }

    .product-item .price {
        font-size: 0.95rem;
    }

    .product-item .old-price {
        font-size: 0.85rem;
    }
}
</style>

</head>
<body>

 <?php include 'includes/header.php'; ?>

    <main class="container">
    <h2>Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword) ?>"</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $discounted_price = $row['price'] * (100 - $row['discount_percentage']) / 100;
            ?>
            <div class="col">
                <div class="product-item">
                    <?php if ($row['discount_percentage'] > 0): ?>
                        <div class="discount-badge">-<?= $row['discount_percentage'] ?>%</div>
                    <?php endif; ?>
                    <img class="product-image" src="admin/uploads/<?= htmlspecialchars($row['image'] ?: 'default.png') ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                    <h5><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="mb-1 text-muted"><?= htmlspecialchars($row['category_name']) ?> - <?= htmlspecialchars($row['brand_name']) ?></p>
                    <div class="price">
                        <?= number_format($discounted_price) ?> đ
                        <?php if ($row['discount_percentage'] > 0): ?>
                            <span class="old-price"><?= number_format($row['price']) ?> đ</span>
                        <?php endif; ?>
                    </div>
                    <p class="rating-stars" title="Đánh giá trung bình: <?= round($row['rating'], 1) ?>">
                        <?= str_repeat('⭐', round($row['rating'])) ?>
                        (<?= $row['rating_count'] ?>)
                    </p>
                    <a href="product_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
        <?php endif; ?>
    </div>
    <?php if ($total_pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page - 1 ?>">Trước</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page + 1 ?>">Sau</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

</main>

<!-- footer -->
<?php include 'includes/footer.php'; ?>   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/breadcrumb.js"></script>
    <script src="assets/js/pagination.js"></script>
    <script src="assets/js/sort.js"></script>
    <script src="assets/js/quantity.js"></script>
    <script src="assets/js/slider.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/breadcrumb.js"></script>
    <script src="assets/js/pagination.js"></script>
    <script src="assets/js/sort.js"></script>
    <script src="assets/js/quantity.js"></script>
    <script src="assets/js/slider.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init({
        duration: 1500, // thời gian hiệu ứng (ms)
        once: false      // chỉ chạy một lần
    });
    </script>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>AOS.init({ duration: 1500, once: false });</script>
    <?php include 'chatbox.php'; ?>

    </body>
</html>
