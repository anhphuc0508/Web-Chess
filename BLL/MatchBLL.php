<?php
/**
 * MatchBLL - Lớp xử lý logic nghiệp vụ cho Match
 * Xử lý: tìm trận đấu đang chơi dở, lưu kết quả, tính ELO
 */
require_once __DIR__ . '/../DAL/MatchDAL.php';
require_once __DIR__ . '/../DAL/MatchHistoryDAL.php';
require_once __DIR__ . '/../DAL/UserDAL.php';

class MatchBLL {
    private $matchDAL;
    private $historyDAL;
    private $userDAL;

    public function __construct($pdo) {
        $this->matchDAL = new MatchDAL($pdo);
        $this->historyDAL = new MatchHistoryDAL($pdo);
        $this->userDAL = new UserDAL($pdo);
    }

    /**
     * Tìm trận đấu đang chơi dở của user
     * @return array|null Thông tin trận đấu và màu quân user cầm
     */
    public function getActiveMatch($userId) {
        $match = $this->matchDAL->findActiveMatch($userId);
        if ($match) {
            $match['myColor'] = ($match['white_id'] == $userId) ? 'w' : 'b';
        }
        return $match;
    }

    /**
     * Lấy thống kê thi đấu của user
     */
    public function getStats($userId) {
        $stats = $this->historyDAL->getStats($userId);
        $stats['win_rate'] = ($stats['total_matches'] > 0) 
            ? round(($stats['total_wins'] / $stats['total_matches']) * 100) 
            : 0;
        return $stats;
    }

    /**
     * Lấy lịch sử thi đấu gần đây (mặc định 5 trận)
     */
    public function getRecentHistory($userId, $limit = 5) {
        return $this->historyDAL->getRecentHistory($userId, $limit);
    }

    /**
     * Lấy toàn bộ lịch sử thi đấu
     */
    public function getAllHistory($userId) {
        return $this->historyDAL->getAllHistory($userId);
    }

    /**
     * Tính hệ số K dựa trên ELO
     */
    private function getKFactor($elo) {
        if ($elo < 1600) {
            return 32;
        } elseif ($elo >= 1600 && $elo < 2400) {
            return 24;
        } else {
            return 16;
        }
    }

    /**
     * Tính ELO mới sau trận đấu
     */
    private function calculateNewElo($myElo, $opElo, $matchResult) {
        $K = $this->getKFactor($myElo);
        $expected = 1 / (1 + pow(10, ($opElo - $myElo) / 400));
        $score = 0.5;
        if ($matchResult === 'win') $score = 1;
        if ($matchResult === 'lose') $score = 0;
        return round($myElo + $K * ($score - $expected));
    }

    /**
     * Lưu kết quả trận đấu (bao gồm tính ELO nếu online)
     * Giữ nguyên logic gốc từ save_match.php
     */
    public function saveMatchResult($userId, $myName, $opponentName, $gameMode, $result, $totalMoves, $isAbandoned, $myColor) {
        // Tìm đối thủ trong database
        $opponent = $this->userDAL->findOpponent($opponentName);

        if ($isAbandoned) {
            // Trường hợp đối thủ bỏ cuộc
            $this->historyDAL->saveMatch($userId, $opponentName, 'online_mode', 'win', $totalMoves);

            if ($opponent) {
                $this->historyDAL->saveMatch($opponent['id'], $myName, 'online_mode', 'lose', $totalMoves);
            }
            $result = 'win'; // Ép thành Win để bên dưới được phép tính Elo
            $gameMode = 'online_mode';
        } else {
            // Lưu kết quả bình thường
            $this->historyDAL->saveMatch($userId, $opponentName, $gameMode, $result, $totalMoves);
        }

        // Tính ELO cho trận online
        $shouldCalcElo = ($result === 'win' || ($result === 'draw' && $myColor === 'w'));

        if ($gameMode === 'online_mode' && $opponent && $shouldCalcElo) {
            $myCurrentElo = $this->userDAL->getElo($userId);
            $opCurrentElo = $opponent['elo'];

            $myNewElo = $this->calculateNewElo($myCurrentElo, $opCurrentElo, $result);

            $opResult = 'draw';
            if ($result === 'win') $opResult = 'lose';
            if ($result === 'lose') $opResult = 'win';
            $opNewElo = $this->calculateNewElo($opCurrentElo, $myCurrentElo, $opResult);

            $this->userDAL->updateElo($userId, $myNewElo);
            $this->userDAL->updateElo($opponent['id'], $opNewElo);

            return ['success' => true, 'newElo' => $myNewElo];
        }

        return ['success' => true, 'newElo' => null];
    }
}
?>
