<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location:  admin_login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "sportshop");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xóa
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $type = $_GET['delete'];
    $id = (int)$_GET['id'];

    if ($type === 'category') {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    } else if ($type === 'brand') {
        $stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
    }
    if (isset($stmt)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_category_brand.php");
    exit;
}

// Thêm/Sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $name = trim($_POST['name']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($type === 'category') {
        if (!empty($name)) {
            if ($id > 0) {
                // Sửa
                $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
                $stmt->bind_param("si", $name, $id);
                $stmt->execute();
            } else {
                // Thêm mới
                $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->bind_param("s", $name);
                $stmt->execute();
            }
            $stmt->close();
        }
    } else {
        // Xử lý thương hiệu
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE brands SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO brands (name) VALUES (?)");
            $stmt->bind_param("s", $name);
        }
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin_category_brand.php");
    exit;
}

// Lấy dữ liệu
$categories = $conn->query("SELECT * FROM categories");
$brands = $conn->query("SELECT * FROM brands");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý Danh mục & Thương hiệu - SportShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="admin_style.css" />
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main">
    <h1>Quản lý Danh mục và Thương hiệu</h1>

    <div class="category-list">
        <h2>Danh mục</h2>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <div class="category-item">
                <div class="category-name"><?= htmlspecialchars($cat['name']) ?></div>
                <div>
                    <button class="btn btn-edit"
                        onclick="openModal('category', <?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>')">Sửa</button>
                    <a href="admin_category_brand.php?delete=category&id=<?= $cat['id'] ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                       <button class="btn btn-delete">Xóa</button>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
        <button class="btn btn-primary" onclick="openModal('category')">+ Thêm danh mục</button>
    </div>

    <div class="brand-list">
        <h2>Thương hiệu</h2>
        <?php while ($brand = $brands->fetch_assoc()): ?>
            <div class="brand-item">
                <div class="brand-name"><?= htmlspecialchars($brand['name']) ?></div>
                <div>
                    <button class="btn btn-edit"
                        onclick="openModal('brand', <?= $brand['id'] ?>, '<?= htmlspecialchars($brand['name'], ENT_QUOTES) ?>')">Sửa</button>
                    <a href="admin_category_brand.php?delete=brand&id=<?= $brand['id'] ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa thương hiệu này?')">
                       <button class="btn btn-delete">Xóa</button>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
        <button class="btn btn-primary" onclick="openModal('brand')">+ Thêm thương hiệu</button>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="modalForm">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle"></h3>
            <form method="post">
                <input type="hidden" name="type" id="modalType">
                <input type="hidden" name="id" id="modalId" value="0">
                <input type="text" name="name" id="inputName" placeholder="Tên danh mục / thương hiệu" required>
                <button type="submit" class="submit-btn">Lưu</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(type, id = 0, name = '') {
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('modalTitle').textContent = (id > 0 ? 'Sửa ' : 'Thêm ') + (type === 'category' ? 'danh mục' : 'thương hiệu');
        document.getElementById('modalType').value = type;
        document.getElementById('modalId').value = id;
        document.getElementById('inputName').value = name;
    }

    function closeModal() {
        document.getElementById('modalOverlay').style.display = 'none';
        document.querySelector('form').reset();
        document.getElementById('modalId').value = 0;
    }
</script>
</body>
</html>