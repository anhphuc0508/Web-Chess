<?php

class MatchDAL {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function findActiveMatch($userId) {
        $sql = "SELECT m.id, m.white_id, m.black_id, u.username as opponent_name, u.elo as opponent_elo 
                FROM matches m 
                JOIN users u ON u.id = CASE WHEN m.white_id = ? THEN m.black_id ELSE m.white_id END 
                WHERE (m.white_id = ? OR m.black_id = ?) AND m.status = 'playing' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
