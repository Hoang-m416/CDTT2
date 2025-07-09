<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");
include 'includes/header.php';
if (!isset($_SESSION['customer_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$customer_id = $_SESSION['customer_id'];

$query = "SELECT SUM(total_amount) AS total_spent FROM orders WHERE customer_id = ? AND status = 'Đã giao thành công'";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Lỗi truy vấn: " . $conn->error);
}
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $row = $result->fetch_assoc();
    $total_spent = $row['total_spent'] ?? 0;
} else {
    $total_spent = 0;
}

// Định nghĩa ưu đãi theo cấp độ
$tiers = [
    "Kim Cương" => [
        "class" => "diamond",
        "icon" => "💎",
        "desc" => "Ưu đãi cao cấp nhất với nhiều phần quà hấp dẫn và dịch vụ chăm sóc đặc biệt.",
        "benefits" => [
            "Miễn phí vận chuyển toàn quốc.",
            "Giảm 20% toàn bộ sản phẩm.",
            "Ưu tiên hỗ trợ khách hàng 24/7.",
            "Quà tặng sinh nhật giá trị.",
            "Mời tham gia sự kiện độc quyền."
        ]
    ],
    "Vàng" => [
        "class" => "gold",
        "icon" => "🥇",
        "desc" => "Nhận ưu đãi hấp dẫn và các voucher giảm giá đặc biệt.",
        "benefits" => [
            "Miễn phí vận chuyển với đơn hàng từ 1 triệu.",
            "Giảm 15% một số sản phẩm chọn lọc.",
            "Ưu tiên hỗ trợ khách hàng trong giờ hành chính.",
            "Voucher giảm giá cho lần mua tiếp theo."
        ]
    ],
    "Bạc" => [
        "class" => "silver",
        "icon" => "🥈",
        "desc" => "Ưu đãi giảm giá sản phẩm và nhiều quà tặng thú vị.",
        "benefits" => [
            "Giảm 10% cho đơn hàng trên 2 triệu.",
            "Quà tặng nhỏ khi mua hàng.",
            "Hỗ trợ khách hàng qua email."
        ]
    ],
    "Đồng" => [
        "class" => "bronze",
        "icon" => "🥉",
        "desc" => "Tham gia ngay để nhận nhiều ưu đãi hơn khi mua sắm.",
        "benefits" => [
            "Nhận thông tin khuyến mãi sớm.",
            "Ưu đãi đặc biệt khi trở thành khách hàng thân thiết."
        ]
    ],
];

// Xác định cấp độ theo tổng chi tiêu
if ($total_spent >= 60000000) {
    $tier = "Kim Cương";
} elseif ($total_spent >= 10000000) {
    $tier = "Vàng";
} elseif ($total_spent >= 1000000) {
    $tier = "Bạc";
} else {
    $tier = "Đồng";
}

$current_tier = $tiers[$tier];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Kiểm Tra Cấp Độ - SportShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        h2{
            padding-top:60px;
        }
        .container {
            flex-grow: 1;
        }

        .tier-card {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem 3rem;
            box-shadow: 0 8px 24px rgb(0 0 0 / 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .tier-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgb(0 0 0 / 0.15);
        }

        .tier-icon {
            font-size: 5rem;
            margin-bottom: 0.75rem;
            user-select: none;
        }

        .tier-name {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 1.2px;
        }

        .tier-range {
            font-size: 1.3rem;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .tier-desc {
            font-size: 1.1rem;
            color: #444;
            margin-bottom: 2rem;
            min-height: 72px;
        }

        .benefits-list {
            text-align: left;
            max-width: 400px;
            margin: 0 auto 2.5rem;
            font-size: 1.05rem;
            color: #333;
        }

        .benefits-list li {
            margin-bottom: 0.6rem;
            position: relative;
            padding-left: 1.4rem;
        }

        .benefits-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #198754; /* màu xanh lá bootstrap */
            font-weight: bold;
        }

        .btn-loyalty {
            padding: 0.75rem 2.5rem;
            font-size: 1.15rem;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }

        /* Các class màu theo cấp độ */
        .diamond {
            color: #0f52ba;
            background: linear-gradient(135deg, #d6e4ff 0%, #a0c4ff 100%);
            box-shadow: 0 0 20px #0f52ba66;
        }

        .gold {
            color: #b58900;
            background: linear-gradient(135deg, #fff8dc 0%, #ffe066 100%);
            box-shadow: 0 0 20px #b5890066;
        }

        .silver {
            color: #6c757d;
            background: linear-gradient(135deg, #e9ecef 0%, #adb5bd 100%);
            box-shadow: 0 0 20px #6c757d66;
        }

        .bronze {
            color: #b87333;
            background: linear-gradient(135deg, #f3e5ab 0%, #d2b48c 100%);
            box-shadow: 0 0 20px #b8733366;
        }

        footer {
            text-align: center;
            padding: 1rem 0;
            font-size: 0.9rem;
            color: #888;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <h2 class="text-center mb-5 fw-bold">Cấp Độ Thành Viên Hiện Tại</h2>
        <div class="tier-card <?= htmlspecialchars($current_tier['class']) ?>">
            <div class="tier-icon"><?= $current_tier['icon'] ?></div>
            <h3 class="tier-name"><?= htmlspecialchars($tier) ?></h3>
            <p class="tier-range">Tổng chi tiêu: <strong><?= number_format($total_spent, 0, ',', '.') ?>₫</strong></p>
            <p class="tier-desc"><?= htmlspecialchars($current_tier['desc']) ?></p>

            <ul class="benefits-list">
                <?php foreach ($current_tier['benefits'] as $benefit): ?>
                    <li><?= htmlspecialchars($benefit) ?></li>
                <?php endforeach; ?>
            </ul>

            <a href="loyalty.php" class="btn btn-primary btn-loyalty">Xem Ưu Đãi Chi Tiết</a>
        </div>
    </div>
                    <?php include 'chatbox.php'; ?>

    <?php include 'includes/footer.php'; ?>
</body>

</html>