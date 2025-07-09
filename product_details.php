<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// Lấy ID từ URL và kiểm tra
$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

// Nếu không hợp lệ thì thoát sớm
if ($product_id <= 0) {
    die("ID sản phẩm không hợp lệ.");
}

// Lấy sản phẩm
$product_sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($product_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Không tìm thấy sản phẩm.");
}

// Lấy size còn hàng
$size_sql = "SELECT * FROM product_sizes WHERE product_id = ? AND quantity > 0";
$size_stmt = $conn->prepare($size_sql);
$size_stmt->bind_param("i", $product_id);
$size_stmt->execute();
$sizes = $size_stmt->get_result();

// Lấy ảnh phụ
$image_sql = "SELECT * FROM product_images WHERE product_id = ?";
$image_stmt = $conn->prepare($image_sql);
$image_stmt->bind_param("i", $product_id);
$image_stmt->execute();
$additional_images = $image_stmt->get_result();

// Lấy sản phẩm liên quan
$category_id = $product['category_id'];
$related_sql = "SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 4";
$related_stmt = $conn->prepare($related_sql);
$related_stmt->bind_param("ii", $category_id, $product_id);
$related_stmt->execute();
$related_products = $related_stmt->get_result();

// Giả sử bạn đã có $product['id']
$stmt = $conn->prepare("SELECT SUM(quantity) AS total_sold FROM order_items WHERE product_id = ?");
$stmt->bind_param("i", $product['id']);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$total_sold = $result['total_sold'] ?? 0;

?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= $product['name'] ?></title>
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
        .qty-btn { width: 40px; height: 40px; font-weight: bold; }
        .quantity-input { width: 60px; text-align: center; }
        .size-btn {
        min-width: 60px;
        border-radius: 25px;
        transition: 0.2s;
        }
        .size-btn.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
        }
        .badge-discount {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 8px 12px;
        font-size: 1rem;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        z-index: 10;
        }

        .card {
  transition: transform 0.2s, box-shadow 0.2s;
  border-radius: 10px;
  overflow: hidden;
}

/* Đảm bảo khoảng cách giữa các card */
.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

/* Huy hiệu giảm giá */
.badge.bg-danger {
  font-size: 0.8rem;
  padding: 0.4em 0.6em;
  border-radius: 0.5rem;
}

/* Tiêu đề sản phẩm */
.card-title {
  font-size: 1rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 0.5rem;
}

/* Giá sản phẩm */
.card-body .text-danger {
  font-size: 1.1rem;
}

.text-decoration-line-through {
  font-size: 0.9rem;
}

/* Xếp hạng sao */
.text-warning {
  font-size: 0.9rem;
}

/* Nút "Xem chi tiết" */
.card .btn-outline-primary {
  transition: all 0.3s ease;
}

.card .btn-outline-primary:hover {
  background-color: #0d6efd;
  color: white;
}

/* Hình ảnh sản phẩm */
.card-img-top {
  border-top-left-radius: 0.5rem;
  border-top-right-radius: 0.5rem;
}

/* Điều chỉnh padding bên trong card body */
.card-body {
  padding: 1rem;
}
.product-image {
  height: 300px;
  object-fit: contain;
  width: 100%;
  background-color: #f8f9fa; /* thêm nền nhẹ nếu ảnh không phủ hết */
}

.size-guide h3, .size-guide h4 {
    margin-top: 15px;
    margin-bottom: 5px;
    color: #333;
}
.size-guide ul {
    margin-left: 20px;
    padding-left: 0;
    list-style-type: disc;
}
.size-columns {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.size-section {
    flex: 1;
    min-width: 180px;
}

.size-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    color: #555;
    table-layout: fixed;
}

.size-table th,
.size-table td {
    border: 1px solid #ddd;
    padding: 6px 8px;
    text-align: center;
    word-wrap: break-word;
}

.size-table th {
    background-color: #f7f7f7;
    font-weight: 600;
    color: #222;
}

.size-table tr:nth-child(even) {
    background-color: #fafafa;
}

@media (max-width: 480px) {
    .size-columns {
        flex-direction: column;
    }
}
body.no-scroll {
    overflow: hidden;
}
h4{
  font: 1.4em sans-serif;
}


    </style>
</heade>
<body>
 <?php include 'includes/header.php'; ?>

 

<body class="bg-light">

<div class="container my-5">
  <div class="row bg-white shadow p-4 rounded">
    <!-- Ảnh -->
    <div class="col-md-5 position-relative text-center">
      <img id="mainProductImage" src="admin/uploads/<?= $product['image'] ?>" class="img-fluid border rounded" alt="<?= $product['name'] ?>" style="max-width: 420px; height: auto;">
      
      <?php if ($additional_images->num_rows > 0): ?>
        <div class="d-flex justify-content-center flex-wrap mt-3 gap-2">
          <?php while ($img = $additional_images->fetch_assoc()): ?>
            <img src="admin/uploads/<?= $img['image'] ?>" 
                 class="img-thumbnail border border-secondary" 
                 style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                 onclick="document.getElementById('mainProductImage').src = this.src">
          <?php endwhile; ?>
        </div>
      <?php endif; ?>

      <?php if ($product['discount_percentage'] > 0): ?>
        <div class="badge-discount bg-danger text-white fw-bold">
          -<?= rtrim(rtrim(number_format($product['discount_percentage'], 2, '.', ''), '0'), '.') ?>%
        </div>
      <?php endif; ?>
    </div>

    <!-- Thông tin -->
    <div class="col-md-7">
      <h3><?= htmlspecialchars($product['name']) ?></h3>
      <div class="mb-3">
        <strong>Giá:</strong>
        <?php
          $discount_price = $product['price'] * (1 - $product['discount_percentage'] / 100);
          echo "<span class='text-danger fs-4'>" . number_format($discount_price, 0, ',', '.') . " ₫</span>";
          if ($product['discount_percentage'] > 0) {
            echo " <span class='text-muted text-decoration-line-through'>" . number_format($product['price'], 0, ',', '.') . " ₫</span>";
          }
        ?>
      </div>

      <!-- Form -->
      <form id="productForm">
        <!-- SIZE -->
        <div class="mb-3">
          <label class="form-label fw-bold">Chọn size:</label><br>
          <?php foreach ($sizes as $size): ?>
            <button type="button"
              class="btn btn-outline-primary size-btn me-2 mb-2"
              data-size="<?= $size['size'] ?>"
              data-quantity="<?= $size['quantity'] ?>">
              <?= $size['size'] ?>
            </button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="size" id="selectedSize">

        <!-- SỐ LƯỢNG -->
        <div class="mb-3 d-flex align-items-center">
          <label class="me-3 mb-0">  <strong>Số lượng:  </strong></label>
          <button type="button" class="btn btn-outline-secondary" onclick="changeQty(-1)">–</button>
          <input type="number" id="quantity" class="form-control form-control-sm mx-2 quantity-input text-center" value="1" min="1" readonly style="width: 60px;">
          <button type="button" class="btn btn-outline-secondary" onclick="changeQty(1)">+</button>
          <span class="ms-3" id="stockInfo" class="text-muted"></span>
        </div>

        <!-- Thông tin khác -->
        <div class="mb-2">
          <strong>Kho:</strong> <span id="availableQuantity">0</span><br>
          <strong>Đã bán:</strong> <?= $total_sold ?><br>
          <strong>Đánh giá:</strong> <?= $product['rating'] ?> ⭐ (<?= $product['rating_count'] ?> đánh giá)
        </div>


        
        <div class="product-options">
    <div class="shipping-info">
        <h4>Vận Chuyển</h4>
        <p id="open-shipping-popup" style="cursor: pointer;">
            Miễn phí vận chuyển cho đơn hàng trên 1.000.000đ &gt;
        </p>
    </div>

    <div class="shipping-info">
        <h4>Hướng Dẫn Chọn Size</h4>
        <p id="open-size-popup" style="cursor: pointer;">
            Chọn đúng size giày & quần áo  &gt;
        </p>
    </div>

    <!-- POPUP PHÍ VẬN CHUYỂN -->
    <div id="shipping-popup" class="popup popup-hidden">
        <div class="popup-content">
            <h3>Thông tin về phí vận chuyển</h3>
            <hr>
            <div class="shipping-method">
                <h4>Nhanh</h4>
                <p style="color: #757575;">Nhận trễ nhất 3 đến 4 ngày</p>
                <p style="color: #757575;">Tặng Voucher ₫15.000 nếu đơn giao sau thời gian trên.</p>
                <p><strong>Miễn phí vận chuyển</strong></p>
            </div>
            <hr>
            <div class="shipping-method">
                <h4>Hỏa Tốc</h4>
                <p>Không hỗ trợ</p>
            </div>
            <hr>
            <div class="shipping-method">
                <h4>Tiết Kiệm</h4>
                <p>Không hỗ trợ</p>
            </div>
            <hr>
            <div class="shipping-method">
                <h4>Tủ Nhận Hàng</h4>
                <p>Không hỗ trợ</p>
            </div>
            <button type="button" class="btn-popup" id="close-shipping-popup">Đã hiểu</button>
        </div>
    </div>

          <!-- POPUP HƯỚNG DẪN CHỌN SIZE -->
        <div id="size-popup" class="popup popup-hidden">
            <div class="popup-content">
                <h3>Hướng dẫn chọn size</h3>
                <hr>
                <div class="size-columns">
                    <div class="size-section">
                        <h4>Giày</h4>
                        <table class="size-table">
                            <thead>
                                <tr><th>Size</th><th>Dài chân (cm)</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>36</td><td>22.5 - 23</td></tr>
                                <tr><td>37</td><td>23 - 23.5</td></tr>
                                <tr><td>38</td><td>23.5 - 24</td></tr>
                                <tr><td>39</td><td>24 - 24.5</td></tr>
                                <tr><td>40</td><td>24.5 - 25</td></tr>
                                <tr><td>41</td><td>25 - 25.5</td></tr>
                                <tr><td>42</td><td>25.5 - 26</td></tr>
                                <tr><td>43</td><td>26 - 26.5</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="size-section">
                        <h4>Quần áo </h4>
                        <table class="size-table">
                            <thead>
                                <tr><th>Size</th><th>Chiều cao</th><th>Cân nặng</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>S</td><td>Dưới 1m60</td><td>Dưới 50kg</td></tr>
                                <tr><td>M</td><td>1m60 - 1m70</td><td>50 - 60kg</td></tr>
                                <tr><td>L</td><td>1m70 - 1m75</td><td>60 - 70kg</td></tr>
                                <tr><td>XL</td><td>Trên 1m75</td><td>Trên 70kg</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <button type="button" class="btn-popup" id="close-size-popup">Đã hiểu</button>
            </div>
        </div>



        <!-- Nút -->
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-primary" onclick="addToCart()">Thêm vào giỏ hàng</button>
        </div>
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
      </form>
    </div>
  </div>
</div>


<!-- Mô tả -->
  <div class="row mt-5">
    <div class="col">
      <div class="bg-white shadow p-4 rounded">
        <h5>Mô tả sản phẩm</h5>
        <p><?= nl2br($product['description']) ?></p>
      </div>
    </div>
  </div>
</div>

<!-- Sản phẩm liên quan -->
<div class="row mt-4">
  <div class="col">
    <div class="bg-white shadow p-4 rounded">
      <h5 class="mb-4">Các sản phẩm liên quan</h5>
      <div class="row">
        <?php while ($rel = $related_products->fetch_assoc()): ?>
          <?php
            $rel_discount_price = $rel['price'] * (1 - $rel['discount_percentage'] / 100);
            $discount = $rel['discount_percentage'];
          ?>
          <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100 position-relative shadow-sm border-0">
              <?php if ($discount > 0): ?>
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                  -<?= rtrim(rtrim($discount, '0'), '.') ?>%
                </span>
              <?php endif; ?>
              <a href="product_details.php?id=<?= $rel['id'] ?>">
                <img src="admin/uploads/<?= $rel['image'] ?>" class="card-img-top rounded-top" style="height: 300px; object-fit: cover;" alt="<?= $rel['name'] ?>">
              </a>
              <div class="card-body d-flex flex-column">
                <h6 class="card-title"><?= $rel['name'] ?></h6>
                <div class="mb-2">
                  <span class="text-danger fw-bold"><?= number_format($rel_discount_price, 0, ',', '.') ?> ₫</span>
                  <?php if ($discount > 0): ?>
                    <small class="text-muted text-decoration-line-through ms-1"><?= number_format($rel['price'], 0, ',', '.') ?> ₫</small>
                  <?php endif; ?>
                </div>
                <div class="mb-2 text-warning">
                  <?= str_repeat("⭐", floor($rel['rating'])) ?>
                  <small class="text-muted">(<?= $rel['rating_count'] ?>)</small>
                </div>
                <a href="product_details.php?id=<?= $rel['id'] ?>" class="btn btn-sm btn-outline-primary mt-auto">Xem chi tiết</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
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
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/breadcrumb.js"></script>
    <script src="assets/js/pagination.js"></script>
    <script src="assets/js/sort.js"></script>
    <script src="assets/js/quantity.js"></script>
    <script src="assets/js/slider.js"></script>
<!-- Script xử lý -->
<script>
let selectedSize = null;
document.querySelectorAll('.size-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedSize = btn.dataset.size;
        document.getElementById('selectedSize').value = selectedSize;
        const availableQty = parseInt(btn.dataset.quantity);
        document.getElementById('availableQuantity').textContent = availableQty;
        document.getElementById('stockInfo').textContent = `Còn ${availableQty} sản phẩm`;
        document.getElementById('quantity').value = 1;
    });
});

function changeQty(change) {
    let qty = parseInt(document.getElementById('quantity').value);
    let maxQty = parseInt(document.getElementById('availableQuantity').textContent) || 1;
    qty += change;
    if (qty < 1) qty = 1;
    if (qty > maxQty) qty = maxQty;
    document.getElementById('quantity').value = qty;
}

function addToCart() {
    if (!selectedSize) {
        showToast("Vui lòng chọn size.");
        return;
    }

    const formData = new FormData(document.getElementById('productForm'));
    formData.append("quantity", document.getElementById('quantity').value);

    fetch("add_to_cart.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data.startsWith("error:")) {
            const message = data.substring(6); // bỏ "error:"
            showToast(message);
        } else if (data.startsWith("success:")) {
            showToast("Đã thêm vào giỏ hàng!");
        } else {
            showToast("Có lỗi xảy ra không xác định.");
        }
    })
    .catch(err => {
        showToast("Không thể kết nối đến máy chủ.");
    });
}
</script>

<script>
document.getElementById("open-shipping-popup").onclick = function () {
    document.getElementById("shipping-popup").classList.remove("popup-hidden");
    document.body.classList.add("no-scroll"); // ⛔ Khóa cuộn
};
document.getElementById("close-shipping-popup").onclick = function () {
    document.getElementById("shipping-popup").classList.add("popup-hidden");
    document.body.classList.remove("no-scroll"); // ✅ Cho cuộn lại
};

document.getElementById("open-size-popup").onclick = function () {
    document.getElementById("size-popup").classList.remove("popup-hidden");
    document.body.classList.add("no-scroll"); // ⛔ Khóa cuộn
};
document.getElementById("close-size-popup").onclick = function () {
    document.getElementById("size-popup").classList.add("popup-hidden");
    document.body.classList.remove("no-scroll"); // ✅ Cho cuộn lại
};


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

    const duration = 2800; // 👈 Chạy nhanh trong 0.8 giây

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

function increase() {
    let qty = document.getElementById('quantity');
    qty.value = parseInt(qty.value) + 1;
}

function decrease() {
    let qty = document.getElementById('quantity');
    if (parseInt(qty.value) > 1) {
        qty.value = parseInt(qty.value) - 1;
    }
}
</script>



</body>
</html>
