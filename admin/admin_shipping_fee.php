<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location:  admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$provinces = [
    "An Giang", "Bà Rịa - Vũng Tàu", "Bạc Liêu", "Bắc Giang", "Bắc Kạn", "Bắc Ninh", "Bến Tre", "Bình Dương",
    "Bình Định", "Bình Phước", "Bình Thuận", "Cà Mau", "Cao Bằng", "Cần Thơ", "Đà Nẵng", "Đắk Lắk", "Đắk Nông",
    "Điện Biên", "Đồng Nai", "Đồng Tháp", "Gia Lai", "Hà Giang", "Hà Nam", "Hà Nội", "Hà Tĩnh", "Hải Dương",
    "Hải Phòng", "Hậu Giang", "Hòa Bình", "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu",
    "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An", "Ninh Bình", "Ninh Thuận", "Phú Thọ",
    "Phú Yên", "Quảng Bình", "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng", "Sơn La",
    "Tây Ninh", "Thái Bình", "Thái Nguyên", "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "TP Hồ Chí Minh",
    "Trà Vinh", "Tuyên Quang", "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
];

// Thêm hoặc cập nhật
if (isset($_POST['save'])) {
    $province = $conn->real_escape_string($_POST['province']);
    $fee = (int)$_POST['fee'];

    if ($province && is_numeric($fee)) {
        $check = $conn->query("SELECT * FROM shipping_fee WHERE province = '$province'");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE shipping_fee SET fee = $fee WHERE province = '$province'");
            $_SESSION['message'] = "Đã cập nhật phí vận chuyển cho $province.";
        } else {
            $conn->query("INSERT INTO shipping_fee (province, fee) VALUES ('$province', $fee)");
            $_SESSION['message'] = "Đã thêm phí vận chuyển cho $province.";
        }
    }
    header("Location: admin_shipping_fee.php");
    exit;
}

// Xóa
if (isset($_GET['delete'])) {
    $province = $conn->real_escape_string($_GET['delete']);
    $conn->query("DELETE FROM shipping_fee WHERE province = '$province'");
    $_SESSION['message'] = "Đã xóa phí vận chuyển cho $province.";
    header("Location: admin_shipping_fee.php");
    exit;
}

// Lấy dữ liệu cho form nếu đang sửa
$edit_province = $_GET['edit'] ?? null;
$edit_fee = '';
if ($edit_province) {
    $res = $conn->query("SELECT fee FROM shipping_fee WHERE province = '" . $conn->real_escape_string($edit_province) . "'");
    if ($row = $res->fetch_assoc()) {
        $edit_fee = $row['fee'];
    }
}

// Danh sách hiện tại
$fees = $conn->query("SELECT * FROM shipping_fee ORDER BY province");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý phí vận chuyển</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main">
    <h1>Quản lý phí vận chuyển</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: green;"><strong><?= $_SESSION['message'] ?></strong></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="post">
        <label for="province">Tỉnh/Thành phố:</label>
        <select name="province" required>
            <option value="">-- Chọn tỉnh --</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?= $province ?>" <?= ($province === $edit_province) ? 'selected' : '' ?>><?= $province ?></option>
            <?php endforeach; ?>
        </select>

        <label for="fee">Phí vận chuyển (VNĐ):</label>
        <input type="number" name="fee" value="<?= htmlspecialchars($edit_fee) ?>" required min="0" />

        <button type="submit" name="save"><?= $edit_province ? "Cập nhật" : "Thêm" ?></button>
    </form>

    <h2>Danh sách phí vận chuyển</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Tỉnh/Thành phố</th>
            <th>Phí vận chuyển (VNĐ)</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $fees->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['province']) ?></td>
                <td><?= number_format($row['fee']) ?></td>
                <td>
                    <a href="?edit=<?= urlencode($row['province']) ?>">Sửa</a> |
                    <a href="?delete=<?= urlencode($row['province']) ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
