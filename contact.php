
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - SportShop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <style>
        
    </style>
</head>

<body>

 <?php include 'includes/header.php'; ?>

    <!-- Contact Banner -->
    <section class="bg-light py-5 text-center">
        <div class="container">
            <br>
            <h2 class="mb-0">Trung tâm trợ giúp khách hàng</h2>
            <p class="lead">Chúng tôi sẵn sàng hỗ trợ bạn 24/7</p>
        </div>
    </section>

    <!-- Contact Info & Form -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Info -->
                <div class="col-md-5">
                    <h4>Thông tin liên hệ</h4>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class='bx bx-buildings'></i> <strong>Tên công ty:</strong> Công ty cổ phần nhiều thành viên</li>
                        <li class="mb-3"><i class='bx bx-map'></i> <strong>Địa chỉ:</strong> 80 Tô Ký, P.TCH, Q.12, TP.HCM</li>
                        <li class="mb-3"><i class='bx bx-envelope'></i> <strong>Email:</strong> support@sportshop.vn</li>
                        <li class="mb-3"><i class='bx bx-phone'></i> <strong>Hotline:</strong> 0123 123 123</li>
                    </ul>
                    <h5>Theo dõi chúng tôi</h5>
                    <a href="#" class="text-dark me-3"><i class='bx bxl-facebook-square bx-sm'></i></a>
                    <a href="#" class="text-dark me-3"><i class='bx bxl-instagram-alt bx-sm'></i></a>
                    <a href="#" class="text-dark"><i class='bx bxl-twitter bx-sm'></i></a>
                </div>

                <!-- Contact Form -->
                <div class="col-md-7">
                    <h4>Gửi tin nhắn cho chúng tôi</h4>
                    <form action="#send_contact.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Địa chỉ Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Chủ đề</label>
                            <input type="text" class="form-control" name="subject" id="subject">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung</label>
                            <textarea class="form-control" name="message" id="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Google Map -->
    <section>
        <div class="container-fluid p-0">
            <iframe src="https://www.google.com/maps?q=80%20T%C3%B4%20K%E1%BB%B9%2C%20P.TCH%2C%20Q.12%2C%20TPHCM&output=embed" 
                    width="100%" height="400" frameborder="0" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="copyright">
            &copy; 2025 SportShop. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            mirror: true
        });

        window.addEventListener('load', function () {
            AOS.refresh();
        });
    </script>
    <?php include 'chatbox.php'; ?>

</body>

</html>
