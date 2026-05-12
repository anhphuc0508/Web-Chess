<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../BLL/FriendBLL.php';

$userId = $_SESSION['user_id'];

$friendBLL = new FriendBLL($pdo);
$friends = $friendBLL->getFriends($userId);

echo json_encode($friends);
?>
