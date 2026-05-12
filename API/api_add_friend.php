<?php

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../BLL/FriendBLL.php';

$senderId = $_SESSION['user_id'];
$receiverId = $_POST['receiver_id'] ?? 0;

$friendBLL = new FriendBLL($pdo);
$result = $friendBLL->sendFriendRequest($senderId, $receiverId);

echo json_encode($result);
?>
