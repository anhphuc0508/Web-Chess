<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../BLL/FriendBLL.php';

$userId = $_SESSION['user_id'];
$requestId = $_POST['request_id'] ?? 0;
$action = $_POST['action'] ?? '';

$friendBLL = new FriendBLL($pdo);
$result = $friendBLL->handleRequest($userId, $requestId, $action);

echo json_encode($result);
?>
