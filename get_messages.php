<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(["error" => "not_logged_in"]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "sportshop");
$conn->set_charset("utf8");

$sender_id = $_SESSION['customer_id'];
$receiver_id = 1; // Admin

$stmt = $conn->prepare("
    SELECT sender, message, created_at 
    FROM messages 
    WHERE 
        (sender_id = ? AND receiver_id = ? AND sender = 'customer') 
        OR 
        (sender_id = ? AND receiver_id = ? AND sender = 'admin')
    ORDER BY created_at ASC
");
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($msg = $result->fetch_assoc()) {
    $messages[] = [
        "sender" => $msg['sender'],
        "message" => htmlspecialchars($msg['message']),
        "created_at" => $msg['created_at']
    ];
}

header('Content-Type: application/json');
echo json_encode($messages);
