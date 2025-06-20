
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu | SportShop</title>
      <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons for icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
        }

        .about-section {
            padding-top: 80px;
            padding-bottom: 80px;
        }

        .about-section img {
            transition: transform 0.4s ease;
        }

        .about-section img:hover {
            transform: scale(1.05);
        }

        h2, h3, h4 {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

            .about-card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 16px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  padding: 40px 30px;
  margin-bottom: 50px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.about-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.section-divider {
  height: 3px;
  background: linear-gradient(to right, #007bff, #00c6ff);
  margin: 60px auto;
  border-radius: 10px;
  opacity: 0.3;
  width: 60%;
}

.about-card ul {
  list-style: none;
  padding-left: 0;
}
.about-card ul li {
  margin-bottom: 10px;
  padding-left: 1.5em;
  position: relative;
}
.about-card ul li::before {
  content: "✔️";
  position: absolute;
  left: 0;
  top: 0;
  color: #198754;
}

    </style>
</head>
<body>
     <?php include 'includes/header.php'; ?>
    <!-- Giới thiệu -->
   <main>
    <br>
    <br>
  <section class="about-section container">
    <h2 class="text-center mb-5" data-aos="fade-down">
      🏃‍♂️ Khám phá hành trình cùng <span class="text-primary">SportShop</span>
    </h2>

    <!-- PHẦN 1: Giới thiệu tổng quát -->
    <div class="about-card" data-aos="fade-up">
      <p class="lead text-center">
        SportShop không đơn thuần là một cửa hàng thể thao – chúng tôi là người đồng hành trên hành trình sống khỏe, sống chất và sống trọn đam mê.
      </p>
      <p class="text-center">
        Ra đời với sứ mệnh <em>"Nâng cao thể chất – Chinh phục giới hạn"</em>, SportShop cam kết cung cấp những sản phẩm chất lượng nhất cùng dịch vụ chăm sóc khách hàng tận tâm nhất.
      </p>
    </div>

    <div class="section-divider"></div>

    <!-- PHẦN 2: Giá trị cốt lõi -->
    <div class="about-card" data-aos="fade-up">
      <h3 class="text-center text-uppercase mb-4">💡 Giá trị cốt lõi</h3>
      <div class="row text-center">
        <div class="col-md-4 mb-3">
          <h5>✅ Chất lượng hàng đầu</h5>
          <p>Tất cả sản phẩm đều được chọn lọc kỹ lưỡng từ các thương hiệu uy tín, mang lại hiệu quả và độ bền vượt trội.</p>
        </div>
        <div class="col-md-4 mb-3">
          <h5>🤝 Phục vụ tận tâm</h5>
          <p>Khách hàng là trung tâm. Đội ngũ nhân viên luôn sẵn sàng hỗ trợ bạn từ A đến Z, kể cả sau mua hàng.</p>
        </div>
        <div class="col-md-4 mb-3">
          <h5>🚀 Truyền cảm hứng</h5>
          <p>SportShop tạo động lực tập luyện với sản phẩm đẹp, hiện đại và giao diện mua sắm truyền cảm hứng mỗi ngày.</p>
        </div>
      </div>
    </div>

    <div class="section-divider"></div>

    <!-- PHẦN 3: Danh mục nổi bật -->
    <div class="about-card" data-aos="fade-up">
      <h3 class="text-center text-uppercase mb-4">🔥 Danh mục nổi bật</h3>
      <div class="row">
        <div class="col-md-4 text-center">
          <img src="assets/images/about/sport-wear.jpg" alt="Trang phục" class="img-fluid rounded mb-3 shadow-sm">
          <h5>Trang phục thể thao</h5>
          <p>Thiết kế thời trang, chất liệu co giãn, thấm hút tốt – hỗ trợ tối đa trong mọi hoạt động.</p>
        </div>
        <div class="col-md-4 text-center">
          <img src="assets/images/about/equipment.jpg" alt="Dụng cụ" class="img-fluid rounded mb-3 shadow-sm">
          <h5>Giày Thể Thao</h5>
          <p>Giày chạy bộ, đá bóng... – đầy đủ cho mọi nhu cầu từ cơ bản đến nâng cao.</p>
        </div>
        <div class="col-md-4 text-center">
          <img src="assets/images/about/accessories.jpg" alt="Phụ kiện" class="img-fluid rounded mb-3 shadow-sm">
          <h5>Phụ kiện tiện ích</h5>
          <p>Balo, dây kháng lực, smartwatch... hỗ trợ bạn trên mọi hành trình thể thao.</p>
        </div>
      </div>
    </div>

    <div class="section-divider"></div>

    <!-- PHẦN 4: Lý do chọn chúng tôi -->
    <div class="about-card" data-aos="zoom-in">
      <h3 class="text-center text-uppercase mb-4">🌟 Vì sao nên chọn SportShop?</h3>
      <ul>
        <li>Hơn 5 năm kinh nghiệm trong ngành bán lẻ thể thao</li>
        <li>Giao hàng nhanh toàn quốc – chỉ 2–3 ngày</li>
        <li>Ưu đãi thành viên, quà tặng hấp dẫn mỗi tháng</li>
        <li>Chính sách đổi trả minh bạch, không rườm rà</li>
      </ul>
    </div>

    <div class="section-divider"></div>

    <!-- PHẦN 5: CTA -->
    <div class="about-card text-center" data-aos="fade-up">
      <h4>🎯 SportShop – Khơi nguồn cảm hứng thể thao mỗi ngày</h4>
      <p>Hãy cùng chúng tôi tạo nên một cộng đồng sống khỏe – sống mạnh mẽ!</p>
      <a href="products.php" class="btn btn-primary mt-3">Xem ngay sản phẩm</a>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/breadcrumb.js"></script>
    <script src="assets/js/pagination.js"></script>
    <script src="assets/js/sort.js"></script>
    <script src="assets/js/quantity.js"></script>
    <script src="assets/js/slider.js"></script>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <script>
        AOS.init({
            duration: 1000,
            offset: 100,
            once: false ,
        });
    </script>
<?php include 'chatbox.php'; ?>
</body>
</html>
