<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($product_id <= 0) {
    echo "Thiếu hoặc sai product_id.";
    exit;
}

// Lấy tên sản phẩm và danh mục
$stmt = $conn->prepare("SELECT p.name, c.name AS category_name FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "Không tìm thấy sản phẩm.";
    exit;
}
$category_name = strtolower($product['category_name']);

// Thêm kho
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $size = strtoupper(trim($_POST['size']));
    $quantity = $_POST['quantity'];

    if (!is_numeric($quantity) || (int)$quantity != $quantity || $quantity == 0) {
        $error = "Vui lòng nhập số lượng là số nguyên khác 0.";
    } else {
        $quantity = (int)$quantity;

        // Nếu là số âm → cần xác nhận
        if ($quantity < 0 && (!isset($_POST['confirm_negative']) || $_POST['confirm_negative'] !== 'yes')) {
            $error = "Bạn đang cố trừ kho. Hãy tick xác nhận để tiếp tục.";
        } else {
            // Kiểm tra nếu size đã tồn tại
            $stmt = $conn->prepare("SELECT id FROM product_sizes WHERE product_id = ? AND size = ?");
            $stmt->bind_param("is", $product_id, $size);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Cộng dồn (hoặc trừ)
                $stmt = $conn->prepare("UPDATE product_sizes SET quantity = quantity + ? WHERE id = ?");
                $stmt->bind_param("ii", $quantity, $row['id']);
                $stmt->execute();
                $success = "Đã cập nhật số lượng $quantity cho size $size.";
            } else {
                if ($quantity < 0) {
                    $error = "Không thể trừ kho cho size chưa tồn tại.";
                } else {
                    $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size, quantity) VALUES (?, ?, ?)");
                    $stmt->bind_param("isi", $product_id, $size, $quantity);
                    $stmt->execute();
                    $success = "Đã thêm size $size với $quantity sản phẩm.";
                }
            }
        }
    }
}

// Lấy danh sách size hiện có
$sizes = [];
$res = $conn->query("SELECT size, quantity FROM product_sizes WHERE product_id = $product_id ORDER BY size ASC");
while ($row = $res->fetch_assoc()) {
    $sizes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập kho - <?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .stock-form { max-width: 500px; margin: 20px auto; }
        .stock-form input, .stock-form select, .stock-form button {
            padding: 10px;
            margin-top: 10px;
            width: 100%;
            font-size: 16px;
        }
        .success { color: green; font-weight: bold; margin-bottom: 10px; }
        .error { color: red; font-weight: bold; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    <h2>Nhập kho: <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['category_name']) ?>)</h2>

    <div class="stock-form">
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Chọn size:</label>
            <select name="size" id="size-select" required></select>

            <label>Số lượng cần nhập (có thể âm):</label>
            <input type="number" name="quantity" required>

            <div id="confirm-box" style="display:none; color: red; margin-top: 10px;">
                <label>
                    <input type="checkbox" name="confirm_negative" value="yes">
                    Tôi xác nhận muốn trừ kho bằng số lượng âm.
                </label>
            </div>

            <button type="submit">Cập nhật kho</button>
        </form>
        <br>
        <a href="products_list.php">← Quay lại danh sách sản phẩm</a>
    </div>

    <h3>Danh sách size đã nhập:</h3>
    <?php if (!empty($sizes)): ?>
    <table>
        <tr>
            <th>Size</th>
            <th>Số lượng tồn</th>
        </tr>
        <?php foreach ($sizes as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['size']) ?></td>
                <td><?= $row['quantity'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p>Chưa có size nào được nhập.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sizeSelect = document.getElementById('size-select');
        const category = "<?= $category_name ?>";
        const quantityInput = document.querySelector('input[name="quantity"]');
        const confirmBox = document.getElementById('confirm-box');

        let sizes = (category.includes('giày') || category.includes('shoe'))
            ? [36, 37, 38, 39, 40, 41, 42, 43]
            : ['S', 'M', 'L', 'XL'];

        sizeSelect.innerHTML = '<option value="">-- Chọn size --</option>';
        sizes.forEach(size => {
            sizeSelect.innerHTML += `<option value="${size}">${size}</option>`;
        });

        quantityInput.addEventListener("input", function () {
            const value = parseInt(quantityInput.value);
            confirmBox.style.display = (value < 0) ? 'block' : 'none';
        });
    });
</script>

</body>
</html>
