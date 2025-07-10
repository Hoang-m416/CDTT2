<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

// ===== THÊM VÀO GIỊ =====
if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['product_id'], $_POST['size'], $_POST['quantity'])
) {
    $product_id = (int)$_POST['product_id'];
    $size = $_POST['size'];
    $quantity = max(1, (int)$_POST['quantity']);
    $name = $_POST['name'] ?? '';
    $image = $_POST['image'] ?? '';
    $key = $product_id . '-' . $size;

    $stmt_price = $conn->prepare("SELECT price, discount_percentage FROM products WHERE id = ?");
    $stmt_price->bind_param("i", $product_id);
    $stmt_price->execute();
    $res_price = $stmt_price->get_result();
    $row_price = $res_price->fetch_assoc();
    $price = round($row_price['price'] * (1 - $row_price['discount_percentage'] / 100));

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$key] = [
            'product_id' => $product_id,
            'name' => $name,
            'image' => $image,
            'size' => $size,
            'price' => $price,
            'quantity' => $quantity
        ];
    }

    if (isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];
        $stmt = $conn->prepare("SELECT id FROM cart_items WHERE customer_id = ? AND product_id = ? AND size = ?");
        $stmt->bind_param("iis", $customer_id, $product_id, $size);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE id = ?");
            $stmt->bind_param("ii", $quantity, $row['id']);
            $stmt->execute();
        } else {
            $created_at = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO cart_items (customer_id, product_id, size, quantity, price, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisids", $customer_id, $product_id, $size, $quantity, $price, $created_at);
            $stmt->execute();
        }
    }

    header("Location: cart.php");
    exit;
}

// ===== CẤP NHẬT SỐ LƯỢNG AJAX =====
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_update'])) {
    $key = $_POST['key'];
    $new_quantity = (int)$_POST['quantity'];

    if (!isset($_SESSION['cart'][$key])) {
        echo 'error: Không tìm thấy sản phẩm trong giỏ';
        exit;
    }

    if (strpos($key, '_') === false) {
        echo 'error: Key không hợp lệ';
        exit;
    }
    list($product_id, $size) = explode('_', $key);

    $stmt = $conn->prepare("SELECT quantity FROM product_sizes WHERE product_id = ? AND size = ?");
    $stmt->bind_param("is", $product_id, $size);
    $stmt->execute();
    $res = $stmt->get_result();

    if (!$row = $res->fetch_assoc()) {
        echo 'error: Không tìm thấy tồn kho sản phẩm';
        exit;
    }

    $stock_quantity = (int)$row['quantity'];

    if ($new_quantity > $stock_quantity) {
        echo 'error: Ưu đãi chỉ áp dụng cho số lượng hiện tại (' . $stock_quantity . ')';
        exit;
    }

    if ($new_quantity <= 0 || $stock_quantity <= 0) {
        unset($_SESSION['cart'][$key]);
    } else {
        $_SESSION['cart'][$key]['quantity'] = $new_quantity;
    }

    $customer_id = $_SESSION['customer']['id'] ?? null;
    if ($customer_id) {
        if ($new_quantity <= 0 || $stock_quantity <= 0) {
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE customer_id = ? AND product_id = ? AND size = ?");
            $stmt->bind_param("iis", $customer_id, $product_id, $size);
            $stmt->execute();
        } else {
            $check_stmt = $conn->prepare("SELECT id FROM cart_items WHERE customer_id = ? AND product_id = ? AND size = ?");
            $check_stmt->bind_param("iis", $customer_id, $product_id, $size);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $update_stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE customer_id = ? AND product_id = ? AND size = ?");
                $update_stmt->bind_param("iiis", $new_quantity, $customer_id, $product_id, $size);
                $update_stmt->execute();
            } else {
                $stmt_price = $conn->prepare("SELECT price, discount_percentage FROM products WHERE id = ?");
                $stmt_price->bind_param("i", $product_id);
                $stmt_price->execute();
                $price_result = $stmt_price->get_result();
                $price_row = $price_result->fetch_assoc();
                $price = round($price_row['price'] * (1 - $price_row['discount_percentage'] / 100));
                $created_at = date('Y-m-d H:i:s');

                $insert_stmt = $conn->prepare("INSERT INTO cart_items (customer_id, product_id, size, quantity, price, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("iisids", $customer_id, $product_id, $size, $new_quantity, $price, $created_at);
                $insert_stmt->execute();
            }
        }
    }

    echo 'OK';
    exit;
}

// ===== XOÁ SẢN PHẨM =====
if (isset($_GET['remove'])) {
    $key = $_GET['remove'];

    if (strpos($key, '_') !== false) {
        list($product_id, $size) = explode('_', $key);
        $product_id = (int)$product_id;
        $size = trim($size);

        if (isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE customer_id = ? AND product_id = ? AND size = ?");
            $stmt->bind_param("iis", $customer_id, $product_id, $size);
            $stmt->execute();
        }

        unset($_SESSION['cart'][$key]);
    }

    header("Location: cart.php");
    exit;
}

// ===== KIỂM TRA LẠI TỒN KHO VÀ CẬP NHẬT GIỊ HÀNG =====
$toast_messages = [];

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if (!isset($item['product_id'], $item['size'], $item['quantity'], $item['name'])) {
            continue;
        }

        $product_id = $item['product_id'];
        $size = $item['size'];
        $quantity = $item['quantity'];
        $name = $item['name'];

        $stmt = $conn->prepare("SELECT quantity FROM product_sizes WHERE product_id = ? AND size = ?");
        $stmt->bind_param("is", $product_id, $size);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $stock_quantity = (int)$row['quantity'];

            if ($quantity > $stock_quantity) {
                if ($stock_quantity <= 0) {
                    unset($_SESSION['cart'][$key]);
                    $toast_messages[] = "Sản phẩm \"{$name}\" size {$size} đã hết hàng và bị xoá khỏi giỏ hàng.";
                } else {
                    $_SESSION['cart'][$key]['quantity'] = $stock_quantity;
                    $toast_messages[] = "Sản phẩm \"{$name}\" size {$size} chỉ còn {$stock_quantity} sản phẩm. Đã cập nhật số lượng.";
                }

                if (isset($_SESSION['customer_id'])) {
                    $customer_id = $_SESSION['customer_id'];

                    if ($stock_quantity <= 0) {
                        $stmt_del = $conn->prepare("DELETE FROM cart_items WHERE customer_id = ? AND product_id = ? AND size = ?");
                        $stmt_del->bind_param("iis", $customer_id, $product_id, $size);
                        $stmt_del->execute();
                    } else {
                        $stmt_upd = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE customer_id = ? AND product_id = ? AND size = ?");
                        $stmt_upd->bind_param("iiis", $stock_quantity, $customer_id, $product_id, $size);
                        $stmt_upd->execute();
                    }
                }
            }

            $stmt_price = $conn->prepare("SELECT price, discount_percentage FROM products WHERE id = ?");
            $stmt_price->bind_param("i", $product_id);
            $stmt_price->execute();
            $res_price = $stmt_price->get_result();
            if ($row_price = $res_price->fetch_assoc()) {
                $discounted_price = round($row_price['price'] * (1 - $row_price['discount_percentage'] / 100));
                $_SESSION['cart'][$key]['price'] = $discounted_price;
            }
        }
    }
}

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $item) {
    if (!isset($item['price'], $item['quantity'])) continue;
    $subtotal += $item['price'] * $item['quantity'];
}

$province = $_POST['province'] ?? ($_SESSION['province'] ?? '');
$_SESSION['province'] = $province;
$shipping_fee = 0;

if ($subtotal >= 1000000) {
    $shipping_fee = 0;
} elseif ($province) {
    $stmt = $conn->prepare("SELECT fee FROM shipping_fee WHERE province = ?");
    $stmt->bind_param("s", $province);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $shipping_fee = (int)$row['fee'];
    }
}

$total = $subtotal + $shipping_fee;
?>




<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Giỏ hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
  <link rel="icon" href="assets/images/favicon.ico">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    .cart-item img { width: 80px; border-radius: 8px; }
    .cart-item { border-bottom: 1px solid #e0e0e0; padding: 15px 0; }
    .cart-summary { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 0 8px rgba(0,0,0,0.05); }
    .price { color: #ee4d2d; font-weight: 600; }
    .total-amount { font-size: 20px; font-weight: bold; color: #d0011b; }
    .select-province { max-width: 300px; }
  </style>
</head>

<body>
<?php include 'includes/header.php'; ?>

<div class="container my-5">
  <h3 class="mb-4 fw-bold">🛒 Giỏ hàng của bạn</h3>

  <?php if (empty($cart)): ?>
    <div class="alert alert-warning">Bạn chưa có sản phẩm nào trong giỏ.</div>
  <?php else: ?>
    <form method="post">
      <div class="row">
        <div class="col-lg-8">
          <?php foreach ($cart as $key => $item): 
    if (
        !isset($item['name'], $item['price'], $item['quantity'], $item['image'], $item['size']) ||
        (int)$item['quantity'] <= 0
    ) {
        continue; // Bỏ qua nếu thiếu thông tin hoặc số lượng <= 0
    }

    $name = $item['name'];
    $image = $item['image'];
    $size = $item['size'];
    $price = $item['price'];
    $quantity = $item['quantity'];
?>
    <div class="row cart-item align-items-center bg-white mb-2 rounded p-3 shadow-sm">
        <div class="col-3 text-center">
            <img src="admin/uploads/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
        </div>
        <div class="col-9">
            <div class="d-flex justify-content-between">
                <div>
                    <h6><?= htmlspecialchars($name) ?></h6>
                    <span class="badge bg-warning text-dark">Size <?= htmlspecialchars($size) ?></span>
                    <div class="price"><?= number_format($price, 0, ',', '.') ?> ₫</div>
                    <div class="mt-2 d-flex align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQuantity('<?= $key ?>', -1)">-</button>
                        <input type="text" readonly class="form-control text-center mx-2" style="width: 60px;" value="<?= $quantity ?>" id="qty-<?= $key ?>">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQuantity('<?= $key ?>', 1)">+</button>
                    </div>
                </div>
                <div>
                    <strong><?= number_format($price * $quantity, 0, ',', '.') ?> ₫</strong>
                    <br><br>
                    <a href="?remove=<?= urlencode($key) ?>" class="btn btn-sm btn-outline-danger">Xoá</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

        </div>

        <div class="col-lg-4">
          <div class="cart-summary">
            <h5 class="mb-3">Tóm tắt đơn hàng</h5>
            <div class="mb-3">
            </div>

            <ul class="list-group list-group-flush mb-3">
              <li class="list-group-item d-flex justify-content-between">
                <span>Tạm tính</span><span><?= number_format($subtotal, 0, ',', '.') ?> ₫</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Phí ship</span>
                <span><?= $shipping_fee == 0 ? '<span class="text-success">Miễn phí</span>' : number_format($shipping_fee, 0, ',', '.') . ' ₫' ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Tổng cộng</span><span class="total-amount"><?= number_format($total, 0, ',', '.') ?> ₫</span>
              </li>
            </ul>

            <div class="d-grid">
              <a href="checkout.php" class="btn btn-danger btn-lg">Thanh toán ngay</a>
            </div>
            <br>
            <a href="products.php" class="btn btn-outline-primary">← Quay lại trang sản phẩm</a>
          </div>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

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

<script>
function changeQuantity(key, delta) {
    const input = document.getElementById('qty-' + key);
    let quantity = parseInt(input.value) + delta;
    if (quantity < 1) quantity = 1;

    fetch('cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            ajax_update: true,
            key: key,
            quantity: quantity
        })
    })
    .then(res => res.text())
    .then(() => location.reload());
}
</script>
<script>
$(document).ready(function () {
    $('.qty-btn').click(function () {
        const button = $(this);
        const key = button.data('key'); // ví dụ: "12-M"
        const input = $('#qty-' + key);
        let quantity = parseInt(input.val());

        if (button.hasClass('plus')) {
            quantity += 1;
        } else if (button.hasClass('minus')) {
            quantity = Math.max(quantity - 1, 0); // Không cho xuống dưới 0
        }

        $.post('cart.php', {
            ajax_update: 1,
            key: key,
            quantity: quantity
        }, function (response) {
            if (response.trim() === 'OK') {
                location.reload(); // Tải lại trang để cập nhật tổng tiền và giao diện
            } else {
                alert(response); // Hiển thị thông báo lỗi từ server (ví dụ: hết hàng)
            }
        });
    });
});
</script>

<script>
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

    const duration = 2000;

    setTimeout(() => {
        progress.style.transition = `width ${duration}ms linear`;
        progress.style.width = '100%';
    }, 20);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.visibility = 'hidden';
        overlay.style.display = 'none';
        progress.style.transition = 'none';
        progress.style.width = '0%';
    }, duration);
}
</script>


<script>
function changeQuantity(key, delta) {
    const input = document.getElementById('qty-' + key);
    let quantity = parseInt(input.value) + delta;
    if (quantity < 1) {
        showToast("Số lượng tối thiểu là 1", "danger");
        return;
    }

    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            ajax_update: true,
            key: key,
            quantity: quantity
        })
    })
    .then(response => response.text())
    .then(result => {
        result = result.trim();
        if (result === 'OK') {
            input.value = quantity;
            showToast("Cập nhật giỏ hàng thành công");
            location.reload(); // Nếu cần update tổng tiền tự động, thì bỏ reload và cập nhật DOM
        } else if (result.startsWith("error:")) {
            showToast(result.substring(6).trim(), "danger");
        } else {
            showToast("Phản hồi không xác định: " + result, "warning");
        }
    })
    .catch(error => {
        console.error("Lỗi:", error);
        showToast("Không thể kết nối máy chủ!", "danger");
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($toast_messages)): ?>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        <?php foreach ($toast_messages as $msg): ?>
            showToast("<?= addslashes($msg) ?>");
        <?php endforeach; ?>
    });
</script>
<?php endif; ?>

</body>
</html>
