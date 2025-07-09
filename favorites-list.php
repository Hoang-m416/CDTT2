<?php
session_start();

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$result = false;

if (isset($_SESSION['customer'])) {
    // Người dùng đã đăng nhập, lấy danh sách từ database
    $customerId = $_SESSION['customer']['id'];

    $stmt = $conn->prepare("
        SELECT f.id as fav_id, p.*
        FROM favorites f
        JOIN products p ON f.product_id = p.id
        WHERE f.customer_id = ?
        ORDER BY f.created_at DESC
    ");

    if ($stmt) {
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = false;
    }
} else {
    // Người dùng chưa đăng nhập, lấy danh sách product_id từ session
    if (!empty($_SESSION['favorites']) && is_array($_SESSION['favorites'])) {
        // Lấy danh sách product_id từ session
        $favoriteIds = $_SESSION['favorites'];

        // Tạo câu SQL lấy sản phẩm theo danh sách id này
        // Để tránh lỗi SQL Injection, dùng prepared statement với nhiều tham số
        $placeholders = implode(',', array_fill(0, count($favoriteIds), '?'));

        $sql = "SELECT * FROM products WHERE id IN ($placeholders) ORDER BY FIELD(id, $placeholders)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Vì dùng 2 lần placeholders, truyền param 2 lần
            $types = str_repeat('i', count($favoriteIds) * 2);
            $params = array_merge($favoriteIds, $favoriteIds);

            // bind_param yêu cầu tham chiếu nên dùng call_user_func_array
            $bind_names[] = $types;
            for ($i = 0; $i < count($params); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $params[$i];
                $bind_names[] = &$$bind_name;
            }

            call_user_func_array([$stmt, 'bind_param'], $bind_names);

            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = false;
        }
    } else {
        // Chưa đăng nhập và không có sản phẩm yêu thích trong session
        $result = false;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Yêu thích</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            color: #333;
        }

        h2 {
            padding-top: 90px;
            text-align: center;
            font-size: 28px;
            color: #4f46e5;
        }

        .product-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 10px 40px;
        }

        .product-item {
            flex: 0 0 240px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.15);
        }

        .product-image {
            text-align
            width: 260px;
            height: 260px;
            object-fit: cover;
            border-radius: 14px;
            background: #e0e7ff;
            margin-bottom: 12px;
        }

        .discount-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #ef4444;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #10b981;
            margin-top: 6px;
        }

        .old-price {
            text-decoration: line-through;
            color: #9ca3af;
            font-size: 14px;
            margin-left: 8px;
        }

        .rating-stars {
            font-size: 15px;
            color: #f59e0b;
            margin: 5px 0;
        }

        .btn-group {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-direction: column;
            width: 100%;
        }

        .btn {
            padding: 8px 12px;
            text-align: center;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s, color 0.3s;
            cursor: pointer;
        }

        .btn-outline-primary {
            background: transparent;
            color: #3b82f6;
            border: 1px solid #3b82f6;
        }

        .btn-outline-primary:hover {
            background: #3b82f6;
            color: white;
        }

        .btn-outline-danger {
            background: transparent;
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .btn-outline-danger:hover {
            background: #ef4444;
            color: white;
        }

        .pagination-controls {
            text-align: center;
            margin-top: 20px;
        }

        .pagination-controls button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<h2>Danh sách yêu thích</h2>
<div class="favorite-scroll-container">
    <div class="product-row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $discounted_price = $row['price'] * (100 - $row['discount_percentage']) / 100;
                ?>
                <div class="product-item">
                    <?php if ($row['discount_percentage'] > 0): ?>
                        <div class="discount-badge">-<?= $row['discount_percentage'] ?>%</div>
                    <?php endif; ?>

                    <img src="admin/uploads/<?= htmlspecialchars($row['image']) ?>" class="product-image" alt="<?= htmlspecialchars($row['name']) ?>" />
                    <h5><?= htmlspecialchars($row['name']) ?></h5>

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

                    <div class="btn-group">
                        <a href="product_details.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary">Xem chi tiết</a>

                       <form class="remove-favorite-form" method="POST" action="remove_favorite.php">
                            <?php if (isset($row['fav_id'])): ?>
                                <input type="hidden" name="favorite_id" value="<?= $row['fav_id'] ?>">
                            <?php else: ?>
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-outline-danger">Bỏ thích</button>
                        </form>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Bạn chưa đăng nhập hoặc chưa có sản phẩm yêu thích.</p>
        <?php endif; ?>
    </div>
</div>


    <!-- PHÂN TRANG -->
    <div class="pagination-controls">
        <button id="prevPage" class="btn btn-outline-primary">← Trước</button>
        <span id="pageIndicator"></span>
        <button id="nextPage" class="btn btn-outline-primary">Tiếp →</button>
    </div>
</div>


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

<script>
document.querySelectorAll(".remove-favorite-form").forEach(form => {
    form.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        fetch("remove_favorite.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            data = data.trim(); // loại bỏ khoảng trắng đầu cuối
            if (data === "deleted") {
                showToast("Đã xóa khỏi yêu thích!");
                location.reload();
            } else {
                showToast("Lỗi khi xóa!");
            }
        })
        .catch(() => showToast("Lỗi mạng!"));
    });
});



// PHÂN TRANG
const products = document.querySelectorAll('.product-item');
const itemsPerPage = 6;
let currentPage = 1;

function showPage(page) {
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    products.forEach((item, index) => {
        item.style.display = (index >= start && index < end) ? 'flex' : 'none';
    });

    document.getElementById('pageIndicator').textContent = `Trang ${page}/${Math.ceil(products.length / itemsPerPage)}`;
    document.getElementById('prevPage').disabled = (page === 1);
    document.getElementById('nextPage').disabled = (end >= products.length);
}

document.getElementById('prevPage').addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
    }
});

document.getElementById('nextPage').addEventListener('click', () => {
    if (currentPage * itemsPerPage < products.length) {
        currentPage++;
        showPage(currentPage);
    }
});

showPage(currentPage);


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

    const duration = 6000; // 👈 Chạy nhanh trong 0.8 giây

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
    <?php include 'chatbox.php'; ?>

</body>
</html>
