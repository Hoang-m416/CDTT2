<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");
include 'includes/header.php';

// Mảng định nghĩa các cấp độ và ưu đãi
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
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Ưu Đãi Thành Viên - SportShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex-grow: 1;
        }

        h1 {
            padding-top:60px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 700;
        }

        .tier-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 24px rgb(0 0 0 / 0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }

        .tier-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgb(0 0 0 / 0.15);
        }

        .tier-icon {
            font-size: 4.5rem;
            margin-bottom: 0.75rem;
            user-select: none;
        }

        .tier-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 1.1px;
        }

        .tier-desc {
            font-size: 1.1rem;
            color: #444;
            margin-bottom: 1.5rem;
            min-height: 64px;
        }

        .benefits-list {
            text-align: left;
            max-width: 450px;
            margin: 0 auto;
            font-size: 1rem;
            color: #333;
        }

        .benefits-list li {
            margin-bottom: 0.5rem;
            position: relative;
            padding-left: 1.3rem;
        }

        .benefits-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #198754;
            font-weight: bold;
        }

        /* Màu sắc cấp độ */
        .diamond {
            color: #0f52ba;
            background: linear-gradient(135deg, #d6e4ff 0%, #a0c4ff 100%);
            box-shadow: 0 0 15px #0f52ba66;
        }

        .gold {
            color: #b58900;
            background: linear-gradient(135deg, #fff8dc 0%, #ffe066 100%);
            box-shadow: 0 0 15px #b5890066;
        }

        .silver {
            color: #6c757d;
            background: linear-gradient(135deg, #e9ecef 0%, #adb5bd 100%);
            box-shadow: 0 0 15px #6c757d66;
        }

        .bronze {
            color: #b87333;
            background: linear-gradient(135deg, #f3e5ab 0%, #d2b48c 100%);
            box-shadow: 0 0 15px #b8733366;
        }

        footer {
            text-align: center;
            padding: 1rem 0;
            font-size: 0.9rem;
            color: #888;
            background-color: #fff;
            border-top: 1px solid #ddd;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <h1>Ưu Đãi Thành Viên SportShop</h1>

        <?php foreach ($tiers as $name => $info) : ?>
            <div class="tier-card <?= htmlspecialchars($info['class']) ?>">
                <div class="tier-icon"><?= $info['icon'] ?></div>
                <h2 class="tier-name"><?= htmlspecialchars($name) ?></h2>
                <p class="tier-desc"><?= htmlspecialchars($info['desc']) ?></p>
                <ul class="benefits-list">
                    <?php foreach ($info['benefits'] as $benefit) : ?>
                        <li><?= htmlspecialchars($benefit) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'chatbox.php'; ?>
</body>

</html>
