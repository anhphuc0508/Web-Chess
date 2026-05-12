<?php

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../BLL/UserBLL.php';

$userId = $_SESSION['user_id'];
$query = $_GET['query'] ?? '';

$userBLL = new UserBLL($pdo);
$users = $userBLL->searchUsers($query, $userId);

echo json_encode($users);
