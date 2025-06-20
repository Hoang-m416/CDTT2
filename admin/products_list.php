<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $check_order = $conn->query("SELECT 1 FROM order_items WHERE product_id = $id LIMIT 1");
    $check_cart = $conn->query("SELECT 1 FROM cart_items WHERE product_id = $id LIMIT 1");

    if ($check_order->num_rows > 0 || $check_cart->num_rows > 0) {
        echo "<script>
            alert('Không thể xóa: Sản phẩm đã được mua hoặc đang có trong giỏ hàng.');
            window.location.href = 'products_list.php';
        </script>";
        exit;
    }

    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: products_list.php");
    exit;
}

// Bộ lọc sản phẩm
$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$color = isset($_GET['color']) ? trim($_GET['color']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;

$sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        JOIN brands b ON p.brand_id = b.id 
        WHERE 1=1";

if ($name !== '') {
    $name = $conn->real_escape_string($name);
    $sql .= " AND p.name LIKE '%$name%'";
}
if ($color !== '') {
    $color = $conn->real_escape_string($color);
    $sql .= " AND p.color LIKE '%$color%'";
}
if ($category_id > 0) {
    $sql .= " AND p.category_id = $category_id";
}
if ($brand_id > 0) {
    $sql .= " AND p.brand_id = $brand_id";
}

$sql .= " ORDER BY p.id DESC";
$products = $conn->query($sql);

// Lấy size và tồn kho theo sản phẩm
$product_sizes = [];
$res = $conn->query("SELECT product_id, size, quantity FROM product_sizes");
while ($row = $res->fetch_assoc()) {
    $product_id = $row['product_id'];
    $size = $row['size'];
    $qty = $row['quantity'];
    $product_sizes[$product_id][] = "$size ($qty)";
}

// Tính số lượng đã bán
$sold_counts = [];
$res = $conn->query("SELECT product_id, SUM(quantity) AS sold_qty FROM order_items GROUP BY product_id");
while ($row = $res->fetch_assoc()) {
    $sold_counts[$row['product_id']] = $row['sold_qty'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="admin_style.css" />
</head>
<body>
    <?php include 'sidebar.php'; ?>
   <div class="main">
    <h2>Danh sách sản phẩm</h2>
    <form method="GET" class="product-search-form" style="margin-bottom: 20px;">
        <input type="text" name="name" placeholder="Tên sản phẩm" value="<?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
        <input type="text" name="color" placeholder="Màu sắc" value="<?= isset($_GET['color']) ? htmlspecialchars($_GET['color']) : '' ?>">
        <select name="category_id">
            <option value="">-- Danh mục --</option>
            <?php
            $res = $conn->query("SELECT * FROM categories");
            while ($cat = $res->fetch_assoc()) {
                $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : '';
                echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
            }
            ?>
        </select>
        <select name="brand_id">
            <option value="">-- Thương hiệu --</option>
            <?php
            $res = $conn->query("SELECT * FROM brands");
            while ($b = $res->fetch_assoc()) {
                $selected = (isset($_GET['brand_id']) && $_GET['brand_id'] == $b['id']) ? 'selected' : '';
                echo "<option value='{$b['id']}' $selected>{$b['name']}</option>";
            }
            ?>
        </select>
        <button type="submit">Tìm</button>
        <a href="products_list.php" style="margin-left:10px;">Reset</a>
    </form>

    <a href="admin_add_product.php">+ Thêm sản phẩm mới</a><br><br>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Tên</th>
            <th>Danh mục</th>
            <th>Thương hiệu</th>
            <th>Giá</th>
            <th>Giảm</th>
            <th>Màu</th>
            <th>Ảnh</th>
            <th>Size & SL</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= htmlspecialchars($row['brand_name']) ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['discount_percentage'] ?>%</td>
                <td><?= htmlspecialchars($row['color']) ?></td>
                <td><img src="uploads/<?= $row['image'] ?>" width="60"></td>
                <td>
                    <?php
                        $pid = $row['id'];
                        $sizes = $product_sizes[$pid] ?? [];
                        $has_out_of_stock = false;

                        if (!empty($sizes)) {
                            echo implode(", ", $sizes);

                            // Kiểm tra nếu có size nào hết hàng
                            foreach ($sizes as $s) {
                                if (preg_match('/\((\d+)\)/', $s, $m) && (int)$m[1] === 0) {
                                    $has_out_of_stock = true;
                                    break;
                                }
                            }

                            if ($has_out_of_stock) {
                                echo "<br><span style='color:red; font-weight:bold;'>Hết hàng - cần nhập kho</span>";
                            }
                        } else {
                            echo "<span style='color:orange;'>Chưa có size</span>";
                        }

                        // Luôn cho nhập kho, không cần điều kiện
                        echo "<br><a href='add_stock.php?product_id=$pid' class='btn-edit'>+ Nhập kho</a>";
                    ?>

                    <br><span style="font-size: 12px; color: #555;">
                        <?= $sold_counts[$pid] ?? 0 ?> đã bán
                    </span>
                </td>

                <td>
                    <a class="btn-edit" href="admin_edit_product.php?id=<?= $row['id'] ?>">Sửa </a> 
                    <a class="btn-delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                </td>

            </tr>
        <?php endwhile; ?>
    </table>
                    </div>
</body>
</html>
