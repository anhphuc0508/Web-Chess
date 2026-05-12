<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../BLL/MatchBLL.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit();
}

$userId = $_SESSION['user_id'];
$myName = $_SESSION['username'];
$opponentName = $_POST['opponent_name'] ?? 'BOT DỄ';
$gameMode = $_POST['game_mode'] ?? 'bot_mode';
$result = $_POST['result'] ?? 'draw';
$totalMoves = (int)($_POST['total_moves'] ?? 0);
$isAbandoned = $_POST['is_abandoned'] ?? false;
$myColor = $_POST['color'] ?? 'w';

try {
    $matchBLL = new MatchBLL($pdo);
    $saveResult = $matchBLL->saveMatchResult($userId, $myName, $opponentName, $gameMode, $result, $totalMoves, $isAbandoned, $myColor);

    // Cập nhật lại Session nếu ELO thay đổi
    if ($saveResult['newElo'] !== null) {
        $_SESSION['elo'] = $saveResult['newElo'];
    }

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
