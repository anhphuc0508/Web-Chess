<?php
$host = 'localhost';
$dbname = 'chess_db'; 
$db_user = 'root';    
$db_pass = 'thaibinh123';        

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối MySQL: " . $e->getMessage());
}
?>