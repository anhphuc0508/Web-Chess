<?php
require_once __DIR__ . '/config.php';

echo "Bắt đầu tạo người dùng...\n";

$password = password_hash('123456', PASSWORD_DEFAULT);
$batchSize = 1000;
$totalBatches = 50; // Tạo 50,000 users

$totalInserted = 0;

for ($b = 0; $b < $totalBatches; $b++) {
    $sql = "INSERT IGNORE INTO users (username, password_hash, elo, nickname) VALUES ";
    $values = [];
    $params = [];
    
    for ($i = 0; $i < $batchSize; $i++) {
        $username = 'player_' . mt_rand(1000000, 9999999) . '_' . ($b * $batchSize + $i);
        $elo = mt_rand(400, 2500);
        $nickname = 'Player ' . mt_rand(10000, 99999);
        
        $values[] = "(?, ?, ?, ?)";
        $params[] = $username;
        $params[] = $password;
        $params[] = $elo;
        $params[] = $nickname;
    }
    
    $sql .= implode(", ", $values);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $totalInserted += $stmt->rowCount();
    echo "Batch " . ($b + 1) . "/" . $totalBatches . ": Đã thêm $batchSize users...\n";
}

echo "Hoàn thành! Thực tế đã thêm $totalInserted người dùng mới.\n";
