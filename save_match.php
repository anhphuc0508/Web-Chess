<?php
session_start();
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];
$my_name = $_SESSION['username'];
$opponent_name = $_POST['opponent_name'] ?? 'BOT DỄ';
$game_mode = $_POST['game_mode'] ?? 'bot_mode';
$result = $_POST['result'] ?? 'draw';
$total_moves = (int)($_POST['total_moves'] ?? 0);
$is_abandoned = $_POST['is_abandoned'] ?? false; 
$my_color = $_POST['color'] ?? 'w';

function getKFactor($elo) {
    if ($elo < 1600) {
        return 32;  
    } elseif ($elo >= 1600 && $elo < 2400) {
        return 24;  
    } else {
        return 16;  
    }
}

function calculateNewElo($myElo, $opElo, $matchResult) {
    $K = getKFactor($myElo); 
    $expected = 1 / (1 + pow(10, ($opElo - $myElo) / 400)); 
    $score = 0.5; 
    if ($matchResult === 'win') $score = 1;
    if ($matchResult === 'lose') $score = 0;
    return round($myElo + $K * ($score - $expected)); 
}

try {
    $stmt_op = $pdo->prepare("SELECT id, elo FROM users WHERE username = ? OR nickname = ? LIMIT 1");
    $stmt_op->execute([$opponent_name, $opponent_name]);
    $opponent = $stmt_op->fetch();

    if ($is_abandoned) {
        $stmt1 = $pdo->prepare("INSERT INTO match_history (user_id, opponent_name, game_mode, result, total_moves) VALUES (?, ?, 'online_mode', 'win', ?)");
        $stmt1->execute([$user_id, $opponent_name, $total_moves]);

        if ($opponent) {
            $stmt3 = $pdo->prepare("INSERT INTO match_history (user_id, opponent_name, game_mode, result, total_moves) VALUES (?, ?, 'online_mode', 'lose', ?)");
            $stmt3->execute([$opponent['id'], $my_name, $total_moves]);
        }
        $result = 'win'; // Ép thành Win để bên dưới được phép tính Elo
        $game_mode = 'online_mode'; 
    } else {
        $stmt = $pdo->prepare("INSERT INTO match_history (user_id, opponent_name, game_mode, result, total_moves) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $opponent_name, $game_mode, $result, $total_moves]);
    }

    $should_calc_elo = ($result === 'win' || ($result === 'draw' && $my_color === 'w'));

    if ($game_mode === 'online_mode' && $opponent && $should_calc_elo) {
        $stmt_me = $pdo->prepare("SELECT elo FROM users WHERE id = ?");
        $stmt_me->execute([$user_id]);
        $me = $stmt_me->fetch();

        $myCurrentElo = $me['elo'];
        $opCurrentElo = $opponent['elo'];

        $myNewElo = calculateNewElo($myCurrentElo, $opCurrentElo, $result);
        
        $opResult = 'draw';
        if ($result === 'win') $opResult = 'lose';
        if ($result === 'lose') $opResult = 'win';
        $opNewElo = calculateNewElo($opCurrentElo, $myCurrentElo, $opResult);

        $update_stmt = $pdo->prepare("UPDATE users SET elo = ? WHERE id = ?");
        $update_stmt->execute([$myNewElo, $user_id]); 
        
        $update_op_stmt = $pdo->prepare("UPDATE users SET elo = ? WHERE id = ?");
        $update_op_stmt->execute([$opNewElo, $opponent['id']]); 

        // Cập nhật lại Session
        $_SESSION['elo'] = $myNewElo;
    }

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>