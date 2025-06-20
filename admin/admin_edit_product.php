<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "sportshop");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$product_id = (int)$_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories");
$brands = $conn->query("SELECT * FROM brands");

// Lấy size hiện có
$sizes_result = $conn->query("SELECT * FROM product_sizes WHERE product_id = $product_id");
$sizes = [];
while ($row = $sizes_result->fetch_assoc()) {
    $sizes[$row['size']] = $row['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category_id = (int)$_POST['category_id'];
    $brand_id = (int)$_POST['brand_id'];
    $price = (float)$_POST['price'];
    $discount = (float)$_POST['discount_percentage'];
    $color = $_POST['color'];
    $image = $_FILES['image']['name'] ?: $product['image'];

    if ($_FILES['image']['name']) {
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . basename($image));
    }

    // Cập nhật bảng products (không thay đổi số lượng nữa)
    $conn->query("UPDATE products SET 
        name='$name', 
        description='$description', 
        category_id=$category_id, 
        brand_id=$brand_id,
        price=$price, 
        discount_percentage=$discount, 
        color='$color', 
        image='$image' 
        WHERE id=$product_id");

    // Thêm ảnh phụ nếu có upload
    if (!empty($_FILES['extra_images']['name'][0])) {
        $target_dir = 'uploads/';
        foreach ($_FILES['extra_images']['name'] as $key => $name) {
            $tmp_name = $_FILES['extra_images']['tmp_name'][$key];
            $unique_name = uniqid() . '_' . basename($name);
            $target_path = $target_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                $stmt_img->bind_param("is", $product_id, $unique_name);
                $stmt_img->execute();
                $stmt_img->close();
            }
        }
    }

    header("Location: products_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="admin_style.css" />
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <h2>Sửa sản phẩm</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Tên:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"><br>

        <label>Mô tả:</label><br>
        <textarea name="description" rows="5" style="width:100%;"><?= htmlspecialchars($product['description']) ?></textarea><br>

        <label>Danh mục:</label><br>
        <select name="category_id">
            <?php while ($c = $categories->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label>Thương hiệu:</label><br>
        <select name="brand_id">
            <?php while ($b = $brands->fetch_assoc()): ?>
                <option value="<?= $b['id'] ?>" <?= $b['id'] == $product['brand_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label>Giá:</label><br>
        <input type="number" name="price" value="<?= $product['price'] ?>"><br>

        <label>Giảm giá (%):</label><br>
        <input type="number" name="discount_percentage" value="<?= $product['discount_percentage'] ?>"><br>

        <label>Màu sắc:</label><br>
        <input type="text" name="color" value="<?= htmlspecialchars($product['color']) ?>"><br>

        <label>Ảnh chính (bỏ trống nếu không đổi):</label><br>
        <input type="file" name="image"><br>
        <img src="uploads/<?= $product['image'] ?>" height="80"><br><br>

        <label>Ảnh phụ (có thể chọn nhiều):</label><br>
        <input type="file" name="extra_images[]" multiple><br>

        <!-- Hiển thị ảnh phụ -->
        <div style="margin-top: 10px;">
            <?php
            $image_results = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id");
            while ($img = $image_results->fetch_assoc()) {
                echo "<img src='uploads/{$img['image']}' height='80' style='margin-right:10px'>";
            }
            ?>
        </div><br>

        <label>Size & số lượng hiện có (chỉ xem):</label><br>
        <ul style="list-style-type: none; padding-left: 0;">
            <?php if (!empty($sizes)): ?>
                <?php foreach ($sizes as $size => $qty): ?>
                    <li>Size <strong><?= htmlspecialchars($size) ?></strong>: <?= $qty ?> chiếc</li>
                <?php endforeach; ?>
            <?php else: ?>
                <li style="color: orange;">Chưa có size</li>
            <?php endif; ?>
        </ul><br>

        <button type="submit">Lưu thay đổi</button>
        <a href="products_list.php">Hủy</a>
    </form>
</div>

</body>
</html>
