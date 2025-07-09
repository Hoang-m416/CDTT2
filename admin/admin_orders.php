<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$toast_message = "";

// Cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("SELECT status, payment_method FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($current_status, $payment_method);
    $stmt->fetch();
    $stmt->close();

    $allowed_transitions = [];

    if ($payment_method === 'MoMo') {
        $allowed_transitions = [
            'Đã thanh toán' => 'Đang xử lý',
            'Đang xử lý' => 'Đang chuẩn bị hàng',
            'Đang chuẩn bị hàng' => 'Đã gửi đơn vận chuyển'
        ];
    } else {
        $allowed_transitions = [
            'Chờ xác nhận' => 'Đang xử lý',
            'Đang xử lý' => 'Đang chuẩn bị hàng',
            'Đang chuẩn bị hàng' => 'Đã gửi đơn vận chuyển'
        ];
    }

    if (isset($allowed_transitions[$current_status]) && $allowed_transitions[$current_status] === $new_status) {
        // Trừ kho nếu đơn thường và lần đầu duyệt
        if ($payment_method !== 'MoMo' && $current_status === 'Chờ xác nhận' && $new_status === 'Đang xử lý') {
            $items = $conn->query("SELECT product_id, size, quantity FROM order_items WHERE order_id = $order_id");
            while ($item = $items->fetch_assoc()) {
                $pid = $item['product_id'];
                $size = $item['size'];
                $qty = $item['quantity'];

                $update = $conn->prepare("UPDATE product_sizes SET quantity = quantity - ? WHERE product_id = ? AND size = ? AND quantity >= ?");
                $update->bind_param("iisi", $qty, $pid, $size, $qty);
                $update->execute();
                $update->close();
            }
        }

        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        $stmt->close();

        $toast_message = "Đã cập nhật trạng thái đơn hàng #$order_id!";
    }
}

$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
$customer_name = isset($_GET['customer_name']) ? trim($_GET['customer_name']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$order_date = isset($_GET['order_date']) ? trim($_GET['order_date']) : '';

$sql = "SELECT orders.id, customers.full_name, order_date, total_amount, status, payment_method
        FROM orders 
        JOIN customers ON orders.customer_id = customers.id 
        WHERE 1=1";

if ($order_id !== '') {
    $sql .= " AND orders.id = " . intval($order_id);
}

if ($customer_name !== '') {
    $customer_name = $conn->real_escape_string($customer_name);
    $sql .= " AND customers.full_name LIKE '%$customer_name%'";
}

if ($status !== '') {
    $status = $conn->real_escape_string($status);
    $sql .= " AND orders.status = '$status'";
}

if ($order_date !== '') {
    $sql .= " AND DATE(orders.order_date) = '$order_date'";
}

$sql .= " ORDER BY orders.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng - Admin</title>
    <link rel="stylesheet" href="admin_style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Phần toast */
        #toast {
            visibility: hidden;
            min-width: 300px;
            max-width: 90%;
            background-color: #fbfbfb;
            border: 2px solid #e6e953;
            color: #171717;
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
        }
        #toastProgress {
            position: absolute;
            top: 0;
            left: 0;
            height: 5px;
            background-color: #0a71a1;
            width: 0%;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
            margin: 0;
            color: #333;
        }
        .main {
            margin-left: 220px;
            padding: 30px;
            background: #fff;
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 220px;
            height: 100%;
            background: #4B49AC;
            padding-top: 60px;
        }
        .sidebar a {
            color: #fff;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #343a40;
        }
        h3 {
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .table th, .table td {
            vertical-align: middle !important;
        }
        form.d-flex {
            gap: 10px;
            align-items: center;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<?php if ($toast_message): ?>
    <div id="toast"><?= $toast_message ?><div id="toastProgress"></div></div>
    <script>
        const toast = document.getElementById('toast');
        const progress = document.getElementById('toastProgress');
        toast.style.visibility = 'visible';
        toast.style.opacity = '1';
        progress.style.width = '0%';

        let percent = 0;
        const interval = setInterval(() => {
            percent += 2;
            progress.style.width = percent + '%';
            if (percent >= 100) {
                clearInterval(interval);
                toast.style.opacity = '0';
                toast.style.visibility = 'hidden';
            }
        }, 30);
    </script>
<?php endif; ?>

<div class="main">
    <h3>🧾 Quản lý đơn hàng</h3>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" name="order_id" class="form-control" placeholder="Mã đơn hàng" value="<?= isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '' ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="customer_name" class="form-control" placeholder="Tên khách hàng" value="<?= isset($_GET['customer_name']) ? htmlspecialchars($_GET['customer_name']) : '' ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                <?php
                $statuses = ['Chờ xác nhận', 'Đang xử lý', 'Đang chuẩn bị hàng', 'Đã gửi đơn vận chuyển', 'Đã giao thành công', 'Đã hủy', 'Đã thanh toán'];
                foreach ($statuses as $s) {
                    $selected = (isset($_GET['status']) && $_GET['status'] === $s) ? 'selected' : '';
                    echo "<option value=\"$s\" $selected>$s</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="order_date" class="form-control" value="<?= isset($_GET['order_date']) ? $_GET['order_date'] : '' ?>">
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-primary">Tìm kiếm</button>
            <a href="admin_orders.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>


    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>PTTT</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="text-center">#<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td class="text-center"><?= $row['order_date'] ?></td>
                    <td class="text-end"><?= number_format($row['total_amount'], 0, ',', '.') ?> ₫</td>
                    <td class="text-center"><?= $row['payment_method'] ?></td>
                    <td class="text-center">
                        <?php
                        $status = $row['status'];
                        if ($status === 'Đã giao thành công') {
                            echo '<span class="badge bg-success">Đã giao thành công</span>';
                        } elseif ($status === 'Đã hủy') {
                            echo '<span class="badge bg-danger">Đã hủy</span>';
                        } else {
                            ?>
                            <form method="POST" class="d-flex justify-content-center">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <select name="new_status" class="form-select form-select-sm">
                                    <?php
                                    $payment = $row['payment_method'];
                                    $current = $row['status'];
                                    $next = [];

                                    if ($payment === 'MoMo') {
                                        $next = [
                                            'Đã thanh toán' => 'Đang xử lý',
                                            'Đang xử lý' => 'Đang chuẩn bị hàng',
                                            'Đang chuẩn bị hàng' => 'Đã gửi đơn vận chuyển',
                    
                                        ];
                                    } else {
                                        $next = [
                                            'Chờ xác nhận' => 'Đang xử lý',
                                            'Đang xử lý' => 'Đang chuẩn bị hàng',
                                            'Đang chuẩn bị hàng' => 'Đã gửi đơn vận chuyển'
                                        ];
                                    }

                                    if (isset($next[$current])) {
                                        echo '<option value="' . $next[$current] . '">' . $next[$current] . '</option>';
                                    }
                                    ?>
                                </select>
                                <button class="btn btn-sm btn-primary">Cập nhật</button>
                            </form>
                            <?php
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Chi tiết</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
