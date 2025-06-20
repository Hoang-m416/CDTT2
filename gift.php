<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SportShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon" />
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <section class="loyalty-section py-5" data-aos="fade-up">
        <div class="container">
            <h2 class="section-title text-center mb-5">
                <i class='bx bxs-crown'></i> Chương Trình Khách Hàng Thân Thiết
            </h2>

            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="tier-box bronze">
                        <i class="fa-solid fa-user-circle icon"></i>
                        <h5 class="tier-name">Đồng</h5>
                        <p class="tier-range">Dưới 1 triệu</p>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="tier-box silver">
                        <i class="fa-solid fa-medal icon"></i>
                        <h5 class="tier-name">Bạc</h5>
                        <p class="tier-range">1 - 3 triệu</p>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="tier-box gold">
                        <i class="fa-solid fa-crown icon"></i>
                        <h5 class="tier-name">Vàng</h5>
                        <p class="tier-range">3 - 6 triệu</p>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="tier-box diamond">
                        <i class="fa-solid fa-gem icon"></i>
                        <h5 class="tier-name">Kim Cương</h5>
                        <p class="tier-range">Trên 6 triệu</p>
                    </div>
                </div>
            </div>

            <div class="row mt-5 g-4 justify-content-center">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="benefit-box">
                        <i class='bx bxs-discount benefit-icon icon-discount'></i>
                        <h6>Voucher 50K hàng tháng</h6>
                        <p>Cho thành viên Vàng trở lên</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="benefit-box">
                        <i class='bx bxs-truck benefit-icon icon-truck'></i>
                        <h6>Miễn phí giao hàng</h6>
                        <p>Không giới hạn cho Kim Cương</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="benefit-box">
                        <i class='bx bx-gift benefit-icon icon-gift'></i>
                        <h6>Quà sinh nhật đặc biệt</h6>
                        <p>Tặng trong tháng sinh nhật</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="200">
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="user_gift.php" class="btn btn-cta">Xem cấp độ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-cta">Đăng nhập để kiểm tra cấp độ</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/breadcrumb.js"></script>
    <script src="assets/js/pagination.js"></script>
    <script src="assets/js/sort.js"></script>
    <script src="assets/js/quantity.js"></script>
    <script src="assets/js/slider.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: false,
            mirror: true,
        });

        window.addEventListener("load", function () {
            AOS.refresh();
        });
    </script>
    <?php include 'chatbox.php'; ?>

</body>

</html>
