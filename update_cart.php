<?php
session_start();
$key = $_GET['key'] ?? '';
$action = $_GET['action'] ?? '';

if (!isset($_SESSION['cart'][$key])) {
    header("Location: cart.php");
    exit;
}

switch ($action) {
    case 'increase':
        $_SESSION['cart'][$key]['quantity']++;
        break;
    case 'decrease':
        if ($_SESSION['cart'][$key]['quantity'] > 1) {
            $_SESSION['cart'][$key]['quantity']--;
        } else {
            unset($_SESSION['cart'][$key]);
        }
        break;
    case 'delete':
        unset($_SESSION['cart'][$key]);
        break;
}

header("Location: cart.php");
exit;
?>
