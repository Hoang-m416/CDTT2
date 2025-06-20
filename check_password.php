<?php
session_start();
if (!isset($_SESSION['customer'])) {
    echo json_encode(['valid' => false]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$user_id = $_SESSION['customer']['id'];
$password = $_POST['password'];

$res = $conn->query("SELECT password FROM customers WHERE id = $user_id");
$row = $res->fetch_assoc();

if ($row && password_verify($password, $row['password'])) {
    echo json_encode(['valid' => true]);
} else {
    echo json_encode(['valid' => false]);
}
