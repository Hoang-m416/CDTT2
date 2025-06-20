<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location:  admin_login.php");
    exit;
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Bật báo lỗi đầy đủ

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách danh mục & thương hiệu
$categories = $conn->query("SELECT * FROM categories");
$brands = $conn->query("SELECT * FROM brands");

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $brand_id = (int)$_POST['brand_id'];
    $price = (float)$_POST['price'];
    $discount_percentage = (float)$_POST['discount_percentage'];
    $color = trim($_POST['color']);
    $sizes = $_POST['sizes'] ?? [];

    // Xử lý ảnh
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $target_dir = "uploads/";
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $unique_name = uniqid('img_', true) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $unique_name);
            $image = $unique_name;
        }
    }
  


    // Tính tổng số lượng
    $total_quantity = 0;
    foreach ($sizes as $qty) {
        $total_quantity += (int)$qty;
    }

    if (!empty($name) && $category_id > 0 && $brand_id > 0 && $total_quantity > 0) {
        // Thêm sản phẩm
        $stmt = $conn->prepare("INSERT INTO products (name, description, category_id, brand_id, price, discount_percentage, quantity, color, image)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiidisss", $name, $description, $category_id, $brand_id, $price, $discount_percentage, $total_quantity, $color, $image);
        $stmt->execute();
        $product_id = $conn->insert_id;
        $stmt->close();

        // Thêm các size vào bảng product_sizes
        foreach ($sizes as $size => $qty) {
            $qty = (int)$qty;
            if ($qty > 0) {
                $stmt_size = $conn->prepare("INSERT INTO product_sizes (product_id, size, quantity) VALUES (?, ?, ?)");
                $stmt_size->bind_param("isi", $product_id, $size, $qty);
                $stmt_size->execute();
                $stmt_size->close();
            }
        }

          // Xử lý ảnh phụ nếu có
    if (!empty($_FILES['extra_images']['name'][0])) {
        $extra_images = $_FILES['extra_images'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        for ($i = 0; $i < count($extra_images['name']); $i++) {
            if ($extra_images['error'][$i] === UPLOAD_ERR_OK &&
                in_array($extra_images['type'][$i], $allowed_types)) {

                $ext = pathinfo($extra_images['name'][$i], PATHINFO_EXTENSION);
                $unique_name = uniqid('extra_', true) . '.' . $ext;
                move_uploaded_file($extra_images['tmp_name'][$i], "uploads/" . $unique_name);

                $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                $stmt_img->bind_param("is", $product_id, $unique_name);
                $stmt_img->execute();
                $stmt_img->close();
            }
        }
    }

        // Chuyển hướng sau khi thêm thành công
        header("Location: products_list.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Thêm Sản Phẩm - SportShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="admin_style.css" />
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main">
    <h1>Thêm Sản Phẩm</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Tên sản phẩm:</label>
        <input type="text" id="name" name="name" required>

       <label for="description">Mô tả sản phẩm:</label>
        <textarea id="description" name="description" rows="5" required 
        style="width:100%; padding:8px; font-size:14px; border:1.5px solid #ccc; border-radius:4px; resize: vertical; overflow-y:auto;"></textarea>


        <label for="category_id">Danh mục:</label>
        <select id="category_id" name="category_id" required>
            <option value="">Chọn danh mục</option>
            <?php while ($category = $categories->fetch_assoc()): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="brand_id">Thương hiệu:</label>
        <select id="brand_id" name="brand_id" required>
            <option value="">Chọn thương hiệu</option>
            <?php while ($brand = $brands->fetch_assoc()): ?>
                <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="price">Giá gốc:</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="discount_percentage">Giảm giá (%):</label>
        <input type="number" id="discount_percentage" name="discount_percentage" step="0.01" required>

        <label for="discounted_price">Giá sau khi giảm:</label>
        <input type="text" id="discounted_price" name="discounted_price" value="0" readonly>

        <label for="total_quantity">Tổng số lượng:</label>
        <input type="text" id="total_quantity" name="total_quantity" value="0" readonly>

        <label for="color">Màu sắc:</label>
        <input type="text" id="color" name="color" required>

        <label for="image">Hình ảnh:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        <label for="extra_images">Ảnh phụ (có thể chọn nhiều):</label>
        <input type="file" id="extra_images" name="extra_images[]" accept="image/*" multiple>


        <label for="sizes">Chọn Size và Số lượng:</label>
        <div style="display: flex; justify-content: space-between;">
            <div class="size-column">
                <div class="size-row">
                    <label for="size_s">Size S:</label>
                    <input type="number" name="sizes[S]" id="size_s" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_m">Size M:</label>
                    <input type="number" name="sizes[M]" id="size_m" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_l">Size L:</label>
                    <input type="number" name="sizes[L]" id="size_l" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_xl">Size XL:</label>
                    <input type="number" name="sizes[XL]" id="size_xl" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
            </div>
            <div class="size-column">
                <div class="size-row">
                    <label for="size_36">Size 36:</label>
                    <input type="number" name="sizes[36]" id="size_36" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_37">Size 37:</label>
                    <input type="number" name="sizes[37]" id="size_37" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_38">Size 38:</label>
                    <input type="number" name="sizes[38]" id="size_38" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_39">Size 39:</label>
                    <input type="number" name="sizes[39]" id="size_39" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_40">Size 40:</label>
                    <input type="number" name="sizes[40]" id="size_40" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_41">Size 41:</label>
                    <input type="number" name="sizes[41]" id="size_41" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_42">Size 42:</label>
                    <input type="number" name="sizes[42]" id="size_42" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
                <div class="size-row">
                    <label for="size_43">Size 43:</label>
                    <input type="number" name="sizes[43]" id="size_43" value="0" min="0" onchange="updateTotalQuantity()">
                </div>
            </div>
        </div>

        <button type="submit">Thêm sản phẩm</button>
    </form>
</div>

<script>
    // Tính giá giảm tự động
    document.getElementById('price').addEventListener('input', calculateDiscountedPrice);
    document.getElementById('discount_percentage').addEventListener('input', calculateDiscountedPrice);

    function calculateDiscountedPrice() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
        const discountedPrice = price - (price * (discountPercentage / 100));
        document.getElementById('discounted_price').value = discountedPrice.toFixed(2) + ' VNĐ';
    }

    // Cập nhật tổng số lượng khi nhập số lượng size
    function updateTotalQuantity() {
        const sizes = [
            'size_s', 'size_m', 'size_l', 'size_xl',
            'size_36', 'size_37', 'size_38', 'size_39',
            'size_40', 'size_41', 'size_42', 'size_43'
        ];
        let total = 0;
        sizes.forEach(size => {
            total += parseInt(document.getElementById(size).value) || 0;
        });
        document.getElementById('total_quantity').value = total;
    }
</script>
</body>
</html>