<?php
session_start();

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ngẫu nhiên 10 sản phẩm
$sql = "
SELECT 
    p.id, p.name, p.description, p.price, p.discount_percentage, p.image, p.rating, p.rating_count,
    c.name AS category_name,
    b.name AS brand_name
FROM products p
JOIN categories c ON p.category_id = c.id
JOIN brands b ON p.brand_id = b.id
LEFT JOIN product_sizes ps ON p.id = ps.product_id
GROUP BY p.id
ORDER BY RAND()
LIMIT 10
";

// Thực thi truy vấn và lưu kết quả vào biến $result
$result = $conn->query($sql);
if (!$result) {
    die("Lỗi truy vấn sản phẩm: " . $conn->error);
}

// Lấy 5 đánh giá khách hàng gần đây kèm thông tin khách
$sql_reviews = "
SELECT 
    pr.rating,
    pr.review,
    c.full_name,
    c.province,
    c.avatar
FROM product_ratings pr
JOIN customers c ON pr.customer_id = c.id
JOIN (
    SELECT customer_id, MAX(created_at) AS latest_review
    FROM product_ratings
    WHERE rating = 5
    GROUP BY customer_id
) AS latest_5star ON pr.customer_id = latest_5star.customer_id AND pr.created_at = latest_5star.latest_review
ORDER BY pr.created_at DESC
LIMIT 5
";

$result_reviews = $conn->query($sql_reviews);
if (!$result_reviews) {
    die("Lỗi truy vấn đánh giá: " . $conn->error);
}


?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SportShop</title>
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

        
        .product-item {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 15px;
    width: 220px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

/* Ảnh sản phẩm */
.product-image {
    width: 100%;
    height: 180px;
    object-fit: contain;
    border-radius: 8px;
    background-color: #f8f8f8;
}

/* Tên sản phẩm */
.product-item h5 {
    font-size: 1rem;
    font-weight: 600;
    margin: 10px 0 5px;
    width: 100%;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Chỉ hiện tối đa 2 dòng */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
    min-height: 2.6em; /* Đảm bảo các tên sản phẩm cao bằng nhau */
}


/* Danh mục + thương hiệu */
.product-item p.text-muted {
    font-size: 0.9rem;
    margin: 0 0 10px;
    width: 100%;
}

/* Giá */
.price {
    font-size: 1.1rem;
    font-weight: bold;
    color: #2c3e50;
    width: 100%;
}

.old-price {
    font-size: 0.9rem;
    color: #999;
    text-decoration: line-through;
    margin-left: 5px;
}

/* Đánh giá */
.rating-stars {
    font-size: 0.9rem;
    color: #f39c12;
    margin: 8px 0;
    width: 100%;
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

    </style>
</head>

<body>
    
     <?php include 'includes/header.php'; ?>
    
    <!--  Home -->
    <section class="home" id="home">
    <div class="home-text" data-aos="fade-right">
        <h1>Chào mừng <br>bạn đến với SportShop</h1>
        <p>Điểm đến lý tưởng cho những ai yêu thể thao! Từ dụng cụ tập luyện đến trang phục thể thao thời trang – chúng tôi có mọi thứ bạn cần, phù hợp mọi lứa tuổi. Cùng bắt đầu hành trình năng động ngay hôm nay.</p>
        <p>Sportshop mở ra cánh cửa chào đón bạn đến với một thế giới thể thao đầy màu sắc! Tại đây, chúng tôi cung cấp những trang phục và dụng cụ tập luyện đa dạng, đáp ứng mọi sở thích và phù hợp với mọi lứa tuổi. Đừng chần chừ, hãy khám phá ngay
            hôm nay.</p>
        <a href="products.php" class="btn">Khám phá ngay</a>
    </div>
    <div class="home-img" data-aos="fade-left">
        <img src="assets/images/users/user3.png" alt="" style="width: 700px; height: auto;border-radius: 15px;">
    </div>
</section>

<!-- About -->
<section class="about" id="about">
    <div class="about-img" data-aos="zoom-in">
        <img src="assets/images/users/user4.png" alt="">
    </div>
    <div class="about-text" data-aos="fade-up">
        <h2>Shop chúng tôi</h2>
        <p>Khám phá Sportshop để hiểu rõ hơn về chúng tôi và lý do bạn nên chọn đồng hành cùng chúng tôi.</p>
        <p>Chúng tôi tự hào là cửa hàng chuyên cung cấp các sản phẩm thể thao chính hãng, chất lượng hàng đầu, đến từ những thương hiệu uy tín trong và ngoài nước. Với niềm đam mê thể thao cháy bỏng, chúng tôi luôn mong muốn được đồng hành cùng bạn trên
            hành trình rèn luyện sức khỏe và chinh phục những mục tiêu. <br><br>Đội ngũ tư vấn tận tâm và giàu kinh nghiệm của chúng tôi luôn sẵn sàng lắng nghe và giúp bạn đưa ra những lựa chọn phù hợp nhất.<br> Bên cạnh đó, quy trình mua sắm tiện lợi, giao
            hàng nhanh chóng và chính sách hậu mãi chu đáo chính là những cam kết để bạn luôn an tâm và tin tưởng khi lựa chọn Sportshop.</p>
        <a href="about.php" class="btn">Xem Thêm</a>
    </div>
</section>

    <!-- products -->
   <section class="products" id="products"  data-aos="fade-up">
            <div class="heading">
                <h2>Các sản phẩm phổ biến</h2>
            </div>
            <div>
                <div class="hostProductsList" id="product-list"  data-aos="fade-up">
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <?php
                            $discounted_price = $row['price'] * (100 - $row['discount_percentage']) / 100;
                            ?>
                            <div class="col">
                                <div class="product-item position-relative">
                                    <?php if ($row['discount_percentage'] > 0) : ?>
                                        <div class="discount-badge">-<?= $row['discount_percentage'] ?>%</div>
                                    <?php endif; ?>

                                    <img class="product-image"src="admin/uploads/<?= htmlspecialchars($row['image'] ?: 'default.png') ?>" alt="<?= htmlspecialchars($row['name']) ?>"data-aos="zoom-out">

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
            </section>
                        

            <section class="customers" id="customers"  data-aos="fade-up">
    <div class="heading">
        <h2>Khách hàng vip</h2>
        <p>Những đánh giá và chia sẻ thực tế từ khách hàng thân thiết của chúng tôi</p>
    </div>
    <!-- container -->
    <div class="customers-container">
        <?php
        if ($result_reviews->num_rows > 0) {
            while ($review = $result_reviews->fetch_assoc()) {
                // Xử lý sao đánh giá (rating)
                $rating = floatval($review['rating']);
                // avatar khách, có ảnh thì dùng, không thì lấy mặc định
                $avatar = !empty($review['avatar']) ? 'assets/images/users/' . $review['avatar'] : 'assets/images/users/avatar_2_1748104399.png';
                ?>
                <div class="box" data-aos="flip-left">
                    <div class="start">
                        <?php
                        // In ra các sao tương ứng rating, hỗ trợ nửa sao
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= floor($rating)) {
                                echo "<i class='bx bxs-star'></i>";
                            } elseif ($i - $rating < 1) {
                                echo "<i class='bx bxs-star-half'></i>";
                            } else {
                                echo "<i class='bx bx-star'></i>";
                            }
                        }
                        ?>
                    </div>
                    <p><?php echo htmlspecialchars($review['review']); ?></p>
                    <h2><?php echo htmlspecialchars($review['full_name']); ?></h2>
                    <small>Khách hàng tại <?php echo htmlspecialchars($review['province']); ?></small>
                    <img src="<?php echo $avatar; ?>" alt="Avatar">
                </div>
                <?php
            }
        } else {
            echo "<p>Chưa có đánh giá nào.</p>";
        }
        ?>
    </div>
</section>

   
<!--  xử lý thông báo thêm vô giỏ, yêu thíchthích -->
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

    <?php include 'includes/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/breadcrumb.js"></script>
    <script src="assets/js/pagination.js"></script>
    <script src="assets/js/sort.js"></script>
    <script src="assets/js/quantity.js"></script>
    <script src="assets/js/slider.js"></script>

    <!-- Thêm trước thẻ đóng </body> -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init({
        duration: 1500, // thời gian hiệu ứng (ms)
        once: false      // chỉ chạy một lần
    });
    </script>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-input");
    const searchResults = document.getElementById("search-results");

    searchInput.addEventListener("keyup", function () {
        const keyword = searchInput.value.trim();
        if (keyword.length < 2) {
            searchResults.innerHTML = "";
            return;
        }

        fetch("ajax_search.php?q=" + encodeURIComponent(keyword))
            .then(response => response.text())
            .then(data => {
                searchResults.innerHTML = data;
                searchResults.style.display = 'block';
            });
    });

    // Ẩn kết quả khi click ra ngoài
    document.addEventListener("click", function (e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.innerHTML = "";
        }
    });
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