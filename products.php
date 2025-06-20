<?php
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh mục, thương hiệu, size cho bộ lọc
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$brands = $conn->query("SELECT id, name FROM brands ORDER BY name");
$sizes = $conn->query("SELECT DISTINCT size FROM product_sizes ORDER BY size");

// Xử lý các bộ lọc từ GET
$whereClauses = [];
$params = [];
$types = "";

if (!empty($_GET['category'])) {
    $whereClauses[] = "p.category_id = ?";
    $params[] = $_GET['category'];
    $types .= "i";
}
if (!empty($_GET['brand'])) {
    $whereClauses[] = "p.brand_id = ?";
    $params[] = $_GET['brand'];
    $types .= "i";
}
if (!empty($_GET['size'])) {
    $whereClauses[] = "ps.size = ?";
    $params[] = $_GET['size'];
    $types .= "s";
}
if (isset($_GET['min_price']) && isset($_GET['max_price']) && is_numeric($_GET['min_price']) && is_numeric($_GET['max_price'])) {
    $whereClauses[] = "(p.price * (100 - p.discount_percentage)/100) BETWEEN ? AND ?";
    $params[] = $_GET['min_price'];
    $params[] = $_GET['max_price'];
    $types .= "dd";
}
if (!empty($_GET['rating'])) {
    $whereClauses[] = "p.rating >= ?";
    $params[] = $_GET['rating'];
    $types .= "d";
}

// Xử lý sắp xếp & phân trang
$sort_order = $_GET['sort'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Câu truy vấn chính
$sql = "
SELECT SQL_CALC_FOUND_ROWS 
    p.id, p.name, p.description, p.price, p.discount_percentage, p.image, p.rating, p.rating_count,
    c.name AS category_name,
    b.name AS brand_name,
    IFNULL(SUM(ps.quantity), 0) AS total_quantity
FROM products p
JOIN categories c ON p.category_id = c.id
JOIN brands b ON p.brand_id = b.id
LEFT JOIN product_sizes ps ON p.id = ps.product_id
";

// WHERE
if ($whereClauses) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// GROUP BY
$sql .= " GROUP BY p.id";

// ORDER BY
if ($sort_order === 'price_asc') {
    $sql .= " ORDER BY p.price ASC";
} elseif ($sort_order === 'price_desc') {
    $sql .= " ORDER BY p.price DESC";
} else {
    $sql .= " ORDER BY p.id DESC";
}

// LIMIT + OFFSET
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Prepare & execute
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Tổng số sản phẩm
    $total_result = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc();
    $total_pages = ceil($total_result['total'] / $limit);
} else {
    die("Lỗi truy vấn: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SportShop - Danh sách sản phẩm</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!--boxicon-->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

    <style>
        body {
            background: #f9f9f9;
        }

        .sidebar {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.1);
            position: sticky;
            top: 20px;
        }

        .product-item {
            background: #fff;
            border-radius: 6px;
            padding: 15px;
            box-shadow: 0 0 6px rgb(0 0 0 / 0.1);
            margin-bottom: 20px;
            height: 100%;
        }

        .product-image {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #e74c3c;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .price {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .old-price {
            text-decoration: line-through;
            color: #999;
            font-weight: 400;
            margin-left: 10px;
            font-size: 0.9rem;
        }

        /* Slider nhỏ hơn để không che số */
        .price-range {
            width: 100%;
        }

        .filter-section {
            margin-bottom: 25px;
        }

        .rating-stars {
            color: #f1c40f;
            font-size: 1rem;
        }

        /* Thu nhỏ slider để không che số */
        input[type=range] {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #ddd;
            outline: none;
            margin: 8px 0;
        }

        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #007bff;
            cursor: pointer;
            border: none;
            margin-top: -6px;
        }

        input[type=range]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #007bff;
            cursor: pointer;
            border: none;
        }

        /* Nút yêu thích và chi tiết */
.btn-favorite {
    background: none;
    border: none;
    color: #e74c3c;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0;
    margin-right: 10px;
}

.favorite-icon {
    transition: transform 0.2s;
}

.btn-favorite:hover .favorite-icon {
    transform: scale(1.3);
}

.btn-outline-primary {
    font-size: 0.85rem;
    padding: 6px 10px;
    border-radius: 6px;
    margin-top: 8px;
}

.product-item .btn-outline-primary {
    display: block;
    margin: 10px auto 0; /* Tự động căn giữa theo chiều ngang */
    text-align: center;
}

.out-of-stock {
    color: red;
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;
}

.in-stock {
    color: green;
    font-style: italic;
}

    </style>
</head>

<body>

  
 <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">

            <aside class="col-md-3">
                <form method="GET" id="filter-form" class="sidebar">

                    <h4>Bộ lọc sản phẩm</h4>

                    <!-- Lọc danh mục -->
                    <div class="filter-section">
                        <label for="category" class="form-label fw-bold">Danh mục</label>
                        <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <?php while ($cat = $categories->fetch_assoc()) : ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Lọc thương hiệu -->
                    <div class="filter-section">
                        <label for="brand" class="form-label fw-bold">Thương hiệu</label>
                        <select name="brand" id="brand" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <?php while ($br = $brands->fetch_assoc()) : ?>
                                <option value="<?= $br['id'] ?>" <?= (isset($_GET['brand']) && $_GET['brand'] == $br['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($br['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Lọc size -->
                    <div class="filter-section">
                        <label for="size" class="form-label fw-bold">Size</label>
                        <select name="size" id="size" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <?php while ($sz = $sizes->fetch_assoc()) : ?>
                                <option value="<?= htmlspecialchars($sz['size']) ?>" <?= (isset($_GET['size']) && $_GET['size'] == $sz['size']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sz['size']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                  <!-- Lọc khoảng giá -->
                    <div class="filter-section mb-4">
                        <label for="price-range" class="form-label fw-bold">Khoảng giá (đơn vị: 1,000đ)</label>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <input type="number" name="min_price" id="min_price" min="0" max="10000000" step="1000"
                                value="<?= isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0 ?>"
                                class="form-control" style="width: 120px;" />
                            <span style="font-weight: bold;">-</span>
                            <input type="number" name="max_price" id="max_price" min="0" max="10000000" step="1000"
                                value="<?= isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000000 ?>"
                                class="form-control" style="width: 120px;" />
                        </div>

                        <div style="position: relative; height: 40px; margin-top: 10px;">
                            <input type="range" min="0" max="10000000" step="1000" value="<?= isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0 ?>" id="minRange" style="width: 100%; pointer-events: auto; z-index: 2;" />
                            <input type="range" min="0" max="10000000" step="1000" value="<?= isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000000 ?>" id="maxRange" style="width: 100%; pointer-events: auto; z-index: 1;" />
                        </div>
                    </div>

                    <script>
                          const minRange = document.getElementById('minRange');
                           const maxRange = document.getElementById('maxRange');
                            const minInput = document.getElementById('min_price');
                            const maxInput = document.getElementById('max_price');
                            const minGap = 10000; // khoảng cách tối thiểu 10,000đ

                            function syncMinRange() {
                                let minVal = parseInt(minRange.value);
                                let maxVal = parseInt(maxRange.value);

                                if (minVal > maxVal - minGap) {
                                    minVal = maxVal - minGap;
                                    minRange.value = minVal;
                        }
                        minInput.value = minVal;
                         }

                        function syncMaxRange() {
                            let minVal = parseInt(minRange.value);
                            let maxVal = parseInt(maxRange.value);

                            if (maxVal < minVal + minGap) {
                                maxVal = minVal + minGap;
                                maxRange.value = maxVal;
                            }
                            maxInput.value = maxVal;
                        }

                        minRange.addEventListener('input', () => {
                            syncMinRange();
                        });

                        maxRange.addEventListener('input', () => {
                            syncMaxRange();
                        });

                        minInput.addEventListener('change', () => {
                            let minVal = parseInt(minInput.value);
                            let maxVal = parseInt(maxInput.value);
                            if (minVal > maxVal - minGap) {
                                minVal = maxVal - minGap;
                                minInput.value = minVal;
                            }
                            minRange.value = minVal;
                        });

                        maxInput.addEventListener('change', () => {
                            let minVal = parseInt(minInput.value);
                            let maxVal = parseInt(maxInput.value);
                            if (maxVal < minVal + minGap) {
                                maxVal = minVal + minGap;
                                maxInput.value = maxVal;
                            }
                            maxRange.value = maxVal;
                        });

                        // Khởi tạo đồng bộ ban đầu
                        syncMinRange();
                        syncMaxRange();
                    </script>

                    <!-- Lọc đánh giá -->
                    <div class="filter-section">
                        <label class="form-label fw-bold">Đánh giá</label>
                        <select name="rating" id="rating" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <option value="4" <?= (isset($_GET['rating']) && $_GET['rating'] == 4) ? 'selected' : '' ?>>Từ 4 sao</option>
                            <option value="3" <?= (isset($_GET['rating']) && $_GET['rating'] == 3) ? 'selected' : '' ?>>Từ 3 sao</option>
                            <option value="2" <?= (isset($_GET['rating']) && $_GET['rating'] == 2) ? 'selected' : '' ?>>Từ 2 sao</option>
                            <option value="1" <?= (isset($_GET['rating']) && $_GET['rating'] == 1) ? 'selected' : '' ?>>Từ 1 sao</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Lọc sản phẩm</button>
                    <a href="products.php" class="btn btn-secondary w-100 mt-2">Xóa bộ lọc</a>
                </form>
            </aside>

            <section class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Danh sách sản phẩm</h2>
                    <form method="GET" class="form-inline">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Sắp xếp theo</option>
                            <option value="price_asc" <?= $sort_order === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                            <option value="price_desc" <?= $sort_order === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                        </select>
                        <input type="hidden" name="page" value="<?= $page ?>">
                    </form>
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-4" data-aos="fade-up">
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <?php $discounted_price = $row['price'] * (100 - $row['discount_percentage']) / 100; ?>
                            <div class="col">
                                <div class="product-item position-relative">
                                    <?php if ($row['discount_percentage'] > 0) : ?>
                                        <div class="discount-badge">-<?= $row['discount_percentage'] ?>%</div>
                                    <?php endif; ?>
                                    <img class="product-image" src="admin/uploads/<?= htmlspecialchars($row['image'] ?: 'default.png') ?>" alt="<?= htmlspecialchars($row['name']) ?>" data-aos="zoom-out">
                                    <h5 class="mt-2"><?= htmlspecialchars($row['name']) ?></h5>
                                    <p class="mb-1 text-muted"><?= htmlspecialchars($row['category_name']) ?> - <?= htmlspecialchars($row['brand_name']) ?></p>
                                    <div class="price">
                                        <?= number_format($discounted_price) ?> đ
                                        <?php if ($row['discount_percentage'] > 0): ?>
                                            <span class="old-price"><?= number_format($row['price']) ?> đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="rating-stars" title="Đánh giá trung bình: <?= round($row['rating'],1) ?>">
                                        <?= str_repeat('⭐', round($row['rating'])) ?>
                                        (<?= $row['rating_count'] ?>)
                                    </p>
                                    <?php if ($row['total_quantity'] <= 0): ?>
                                        <p class="out-of-stock" >HẾT HÀNG !!!</p>
                                    <?php else: ?>
                                        <p class="in-stock">Còn hàng: <?= $row['total_quantity'] ?></p>
                                    <?php endif; ?>
                                    <button class="btn-favorite" data-id="<?= $row['id'] ?>">
                                        <span class="favorite-icon">❤</span>
                                    </button>
                                    <a href="product_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p>Không có sản phẩm nào phù hợp.</p>
                    <?php endif; ?>
                </div>
                <!-- Phân trang -->
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort_order ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </section>

        </div>
    </div>


<div id="toast" style="
    visibility: hidden;
    min-width: 300px;
    max-width: 90%;
    background-color: rgb(251, 251, 251);
     border: 2px solidrgb(230, 233, 83);
    color: rgb(23, 23, 23);
    text-align: center;
    border-radius: 15px;
    padding: 18px 28px 24px;
    position: fixed;
    z-index: 9999;
    top: 40px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 20px;
    transition: visibility 0s, opacity 0.5s linear;
    opacity: 0;
    box-shadow: 0 4px 12px rgba(2, 255, 230, 0.39);
    overflow: hidden;
">
    <div id="toastProgress" style="
        position: absolute;
        top: 0;
        left: 0;
        height: 5px;
        background-color:rgb(10, 113, 161);
        width: 0%;
    "></div>
</div>

<div id="toastOverlay" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
"></div>

  <!-- footer -->
  <?php include 'includes/footer.php'; ?> 

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

    <script>
    document.querySelectorAll('.btn-favorite').forEach(button => {
    button.addEventListener('click', function () {
        const productId = this.dataset.id;
        fetch('add_to_favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `product_id=${encodeURIComponent(productId)}`
        })
        .then(res => res.text())
        .then(data => {
            console.log("Phản hồi từ server:", data);
            data = data.trim();

            if (data === 'added' || data === 'added_session') {
                showToast('Đã thêm vào yêu thích!');
                button.classList.add('favorited');
                button.innerHTML = '<i class="fa-solid fa-heart"></i>';
            } else if (data === 'exists' || data === 'exists_session') {
                showToast('Sản phẩm đã nằm trong danh sách yêu thích!');
            } else {
                showToast('Có lỗi xảy ra!');
            }
        })
        .catch(err => {
            console.error("Lỗi fetch:", err);
            showToast('Lỗi kết nối!');
        });
    });
});

function showToast(message) {
    const toast = document.getElementById('toast');
    const progress = document.getElementById('toastProgress');
    const overlay = document.getElementById('toastOverlay');

    toast.textContent = message;
    toast.appendChild(progress);
    toast.style.visibility = 'visible';
    toast.style.opacity = '1';

    overlay.style.display = 'block';

    progress.style.width = '0%';
    progress.style.transition = 'none';

    const duration = 800; // 👈 Chạy nhanh trong 0.8 giây

    setTimeout(() => {
        progress.style.transition = `width ${duration}ms linear`;
        progress.style.width = '100%';
    }, 20); // delay nhỏ để animation bắt đầu mượt hơn

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.visibility = 'hidden';
        overlay.style.display = 'none';
        progress.style.transition = 'none';
        progress.style.width = '0%';
    }, duration);
}
</script>
<?php include 'chatbox.php'; ?>

</body>

</html>

<?php
$conn->close();
?>
