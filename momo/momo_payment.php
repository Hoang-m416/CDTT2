<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header("Location: ../login.php");
    exit;
}

// === Nhận lại thông tin từ session ===
$amount = $_SESSION['momo_total_amount'] ?? 0;
$delivery_address = $_SESSION['momo_delivery_address'] ?? '';
$use_different_address = $_SESSION['momo_use_different_address'] ?? 0;

// === Cấu hình MoMo chính thức ===
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMO4MUD20240115_TEST';
$accessKey = 'Ekj9og2VnRfOuIys';
$secretKey = 'PseUbm2s8QVJEbexsh8H3Jz2qa9tDqoa';

// === Thông tin đơn hàng ===
$orderId = time();
$requestId = time();
$orderInfo = "Thanh toán đơn hàng #" . $orderId;
$redirectUrl = "http://localhost/sportshop/momo_return.php";
$ipnUrl = "http://localhost/sportshop/ipn_momo.php";
$extraData = ""; // Có thể chứa thông tin bổ sung như mã giảm giá, coupon...

// === Loại thanh toán ATM ===
$requestType = "payWithATM";

// === Tạo chữ ký SHA256 ===
$rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
$signature = hash_hmac("sha256", $rawHash, $secretKey);

// === Dữ liệu gửi đến MoMo ===
$data = [
    'partnerCode' => $partnerCode,
    'accessKey' => $accessKey,
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature,
    'lang' => 'vi'
];

// === Gửi request đến MoMo ===
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$result = curl_exec($ch);
curl_close($ch);

// === Xử lý phản hồi ===
$jsonResult = json_decode($result, true);

// Ghi log (tuỳ chọn)
file_put_contents("log_momo.txt", date('Y-m-d H:i:s') . " - Response: " . $result . "\n", FILE_APPEND);

if (isset($jsonResult['payUrl'])) {
    header("Location: " . $jsonResult['payUrl']);
    exit;
} else {
    echo "<h3>Không thể kết nối MoMo (ATM): " . htmlspecialchars($jsonResult['message'] ?? 'Lỗi không xác định') . "</h3>";
}
