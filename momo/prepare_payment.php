<?php
session_start();

// Nhận thông tin từ query string
$_SESSION['momo_total_amount'] = $_GET['amount'] ?? 0;
$_SESSION['momo_delivery_address'] = $_GET['delivery_address'] ?? '';
$_SESSION['momo_use_different_address'] = $_GET['use_different_address'] ?? 0;

// Chuyển hướng tới trang xử lý thanh toán MoMo
header("Location: momo_payment.php");
exit;
