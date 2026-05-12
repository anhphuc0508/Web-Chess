<?php
/**
 * MatchHistoryDAL - Lớp truy cập dữ liệu cho bảng match_history
 * Chứa toàn bộ các truy vấn SQL liên quan đến lịch sử thi đấu
 */
class MatchHistoryDAL {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lấy thống kê: tổng trận, thắng, thua
     */
    public function getStats($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(id) as total_matches, 
                                            SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as total_wins, 
                                            SUM(CASE WHEN result = 'lose' THEN 1 ELSE 0 END) as total_loses 
                                     FROM match_history WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách lịch sử thi đấu (có giới hạn số lượng)
     */
    public function getRecentHistory($userId, $limit = 5) {
        $stmt = $this->pdo->prepare("SELECT opponent_name, game_mode, result, total_moves, played_at 
                                     FROM match_history WHERE user_id = ? 
                                     ORDER BY played_at DESC LIMIT ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy toàn bộ lịch sử thi đấu
     */
    public function getAllHistory($userId) {
        $stmt = $this->pdo->prepare("SELECT opponent_name, game_mode, result, total_moves, played_at 
                                     FROM match_history WHERE user_id = ? 
                                     ORDER BY played_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lưu kết quả trận đấu
     */
    public function saveMatch($userId, $opponentName, $gameMode, $result, $totalMoves) {
        $stmt = $this->pdo->prepare("INSERT INTO match_history (user_id, opponent_name, game_mode, result, total_moves) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $opponentName, $gameMode, $result, $totalMoves]);
    }
}
?>
