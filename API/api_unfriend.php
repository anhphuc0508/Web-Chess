<?php

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../BLL/FriendBLL.php';

$userId = $_SESSION['user_id'];
$friendId = $_POST['friend_id'] ?? 0;

$friendBLL = new FriendBLL($pdo);
$result = $friendBLL->unfriend($userId, $friendId);

echo json_encode($result);
?>
