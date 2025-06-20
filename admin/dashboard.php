<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// =============================
// TỔNG QUAN
// =============================
$total_products = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'] ?? 0;
$total_categories = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'] ?? 0;
$total_brands = $conn->query("SELECT COUNT(*) AS total FROM brands")->fetch_assoc()['total'] ?? 0;
$total_customers = $conn->query("SELECT COUNT(DISTINCT customer_id) AS total FROM orders WHERE status = 'Đã giao thành công'")->fetch_assoc()['total'] ?? 0;
$total_revenue = $conn->query("SELECT SUM(total_amount) AS total FROM orders WHERE status = 'Đã giao thành công'")->fetch_assoc()['total'] ?? 0;

// =============================
// 🗓 NHẬN DỮ LIỆU CHỌN NĂM + THÁNG
// =============================
$selected_year = isset($_POST['selected_year']) ? (int)$_POST['selected_year'] : (int)date('Y');
$selected_month = isset($_POST['selected_month']) ? (int)$_POST['selected_month'] : (int)date('m');

// =============================
// 📊 DOANH THU THEO THÁNG TRONG NĂM ĐƯỢC CHỌN
// =============================
$revenueByMonth = array_fill(1, 12, 0);

$stmt_month = $conn->prepare("
    SELECT MONTH(order_date) AS month, SUM(total_amount) AS revenue
    FROM orders
    WHERE status = 'Đã giao thành công' AND YEAR(order_date) = ?
    GROUP BY MONTH(order_date)
");
$stmt_month->bind_param("i", $selected_year);
$stmt_month->execute();
$resultMonthly = $stmt_month->get_result();

while ($row = $resultMonthly->fetch_assoc()) {
    $revenueByMonth[(int)$row['month']] = (int)$row['revenue'];
}

// =============================
// 📆 DOANH THU THEO NGÀY TRONG THÁNG/NĂM ĐƯỢC CHỌN
// =============================
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);
$revenueByDay = array_fill(1, $daysInMonth, 0);

$stmt_day = $conn->prepare("
    SELECT DAY(order_date) AS day, SUM(total_amount) AS revenue
    FROM orders
    WHERE status = 'Đã giao thành công' AND YEAR(order_date) = ? AND MONTH(order_date) = ?
    GROUP BY DAY(order_date)
");
$stmt_day->bind_param("ii", $selected_year, $selected_month);
$stmt_day->execute();
$resultDaily = $stmt_day->get_result();

while ($row = $resultDaily->fetch_assoc()) {
    $revenueByDay[(int)$row['day']] = (int)$row['revenue'];
}

// =============================
// 🔝 TOP 5 SẢN PHẨM BÁN CHẠY
// =============================
$bestSellerLabels = [];
$bestSellerData = [];
$resultBestSellers = $conn->query("
    SELECT p.name, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    WHERE o.status = 'Đã giao thành công'
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");

while ($row = $resultBestSellers->fetch_assoc()) {
    $bestSellerLabels[] = $row['name'];
    $bestSellerData[] = $row['total_sold'];
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SportShop</title>
    <link rel="stylesheet" href="admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding: 20px;
        }

        .main {
            margin-left: 260px;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            min-height: 100vh;
        }

        .stats ul {
    list-style: none;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 20px;
    padding: 0;
}

.stats li {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f0f4f8; /* nền xám xanh nhẹ */
    color: #222;
    padding: 25px 15px;
    border-left: 6px solid #0d6efd;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stats li:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

.stats li .icon {
    font-size: 32px;
    color: #0d6efd;
}

.stats li strong {
    font-size: 30px;
    font-weight: bold;
    color: #111;
}

.stats li span {
    font-size: 14px;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 1px;
}



        .chart {
            background-color: #ffffff;
            padding: 20px;
            margin-top: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        }

        .btn-toggle {
            padding: 10px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            color: white;
        }

        #btnMonth { background: #0d6efd; }
        #btnDay { background: #6c757d; }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main">
    <h1>Dashboard</h1>
    <p>Chào mừng bạn đến với trang quản trị SportShop.</p>

    <section class="stats">
        <h2>Thống kê nhanh</h2>
        <ul>
            <li>
                <div class="icon">📦</div>
                <strong><?= $total_products ?></strong>
                <span>Sản phẩm</span>
            </li>
            <li>
                <div class="icon">🏷️</div>
                <strong><?= $total_brands ?></strong>
                <span>Thương hiệu</span>
            </li>
            <li>
                <div class="icon">👤</div>
                <strong><?= $total_customers ?></strong>
                <span>Khách hàng</span>
            </li>
            <li>
                <div class="icon">💰</div>
                <strong><?= number_format($total_revenue, 0, ',', '.') ?> đ</strong>
                <span>Doanh thu</span>
            </li>
        </ul>
    </section>


    <section class="chart mt-5 d-flex align-items-start justify-content-between flex-wrap" style="gap: 20px;">
        <div style="flex: 1 1 200px; max-width: 300px;">
            <h2>🔥 Top 5 sản phẩm bán chạy</h2>
            <canvas id="bestsellerChart" height="300"></canvas>
        </div>
        <div style="flex: 1 1 200px; max-width: 300px;">
            <h3>📋 Ghi chú</h3>
            <ul id="bestsellerLegend" style="list-style: none; padding: 0; font-size: 15px;"></ul>
        </div>
    </section>

    <section class="chart mt-5">
        <h2>📊 Doanh thu</h2>
        <form method="POST" style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
    <label>Tháng:
        <select name="selected_month">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= ($m == $selected_month) ? 'selected' : '' ?>><?= $m ?></option>
            <?php endfor; ?>
        </select>
    </label>

    <label>Năm:
        <select name="selected_year">
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                <option value="<?= $y ?>" <?= ($y == $selected_year) ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </label>

    <button type="submit" style="padding: 6px 12px;">Xem</button>
</form>

        <div style="margin-bottom: 20px;">
            <button onclick="toggleChart('month')" id="btnMonth" class="btn-toggle">Doanh thu theo tháng</button>
            <button onclick="toggleChart('day')" id="btnDay" class="btn-toggle">Doanh thu theo ngày</button>
        </div>

        <canvas id="revenueChartMonth" height="100"></canvas>
        <canvas id="revenueChartDay" height="100" style="display:none;"></canvas>
    </section>
</div>

<script>
function toggleChart(type) {
    const chartMonth = document.getElementById('revenueChartMonth');
    const chartDay = document.getElementById('revenueChartDay');
    const btnMonth = document.getElementById('btnMonth');
    const btnDay = document.getElementById('btnDay');

    if (type === 'month') {
        chartMonth.style.display = 'block';
        chartDay.style.display = 'none';
        btnMonth.style.background = '#0d6efd';
        btnDay.style.background = '#6c757d';
    } else {
        chartMonth.style.display = 'none';
        chartDay.style.display = 'block';
        btnMonth.style.background = '#6c757d';
        btnDay.style.background = '#0d6efd';
    }
}

// Doanh thu theo tháng
const monthCtx = document.getElementById('revenueChartMonth').getContext('2d');
new Chart(monthCtx, {
    type: 'bar',
    data: {
        labels: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode(array_values($revenueByMonth)) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => value.toLocaleString('vi-VN') + ' đ'
                }
            }
        }
    }
});

// Doanh thu theo ngày
const dayCtx = document.getElementById('revenueChartDay').getContext('2d');
new Chart(dayCtx, {
    type: 'line',
    data: {
        labels: [...Array(31).keys()].map(i => 'Ngày ' + (i + 1)),
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode(array_values($revenueByDay)) ?>,
            fill: false,
            borderColor: 'rgba(255, 99, 132, 1)',
            tension: 0.3
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => value.toLocaleString('vi-VN') + ' đ'
                }
            }
        }
    }
});

// Top sản phẩm
const bestSellerLabels = <?= json_encode($bestSellerLabels) ?>;
const bestSellerData = <?= json_encode($bestSellerData) ?>;
const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#8e44ad', '#2ecc71'];

const bestsellerCtx = document.getElementById('bestsellerChart').getContext('2d');
new Chart(bestsellerCtx, {
    type: 'pie',
    data: {
        labels: bestSellerLabels,
        datasets: [{
            data: bestSellerData,
            backgroundColor: colors
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

const legendContainer = document.getElementById('bestsellerLegend');
bestSellerLabels.forEach((label, index) => {
    const li = document.createElement('li');
    li.innerHTML = `<span style="display:inline-block;width:14px;height:14px;background:${colors[index]};margin-right:8px;border-radius:3px;"></span>${label}`;
    legendContainer.appendChild(li);
});
</script>
</body>
</html>
