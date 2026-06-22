<?php

class UserDAL {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT id, username, password_hash, nickname, avatar, elo FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function usernameExists($username) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() ? true : false;
    }

    
    public function createUser($username, $hashedPassword) {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password_hash, elo) VALUES (:username, :password_hash, 400)");
        return $stmt->execute(['username' => $username, 'password_hash' => $hashedPassword]);
    }


    public function getUserById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getUserBasicInfo($userId) {
        $stmt = $this->pdo->prepare("SELECT username, nickname, elo, avatar FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getUserDisplayInfo($userId) {
        $stmt = $this->pdo->prepare("SELECT username, nickname, avatar FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function nicknameExistsExcluding($nickname, $userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE nickname = ? AND id != ?");
        $stmt->execute([$nickname, $userId]);
        return $stmt->fetchColumn() > 0;
    }

    
    public function updateNickname($userId, $nickname) {
        $stmt = $this->pdo->prepare("UPDATE users SET nickname = ? WHERE id = ?");
        return $stmt->execute([$nickname, $userId]);
    }

   
    public function updateAvatar($userId, $avatarPath) {
        $stmt = $this->pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        return $stmt->execute([$avatarPath, $userId]);
    }

   
    public function getElo($userId) {
        $stmt = $this->pdo->prepare("SELECT elo FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['elo'] : null;
    }

   
    public function updateElo($userId, $newElo) {
        $stmt = $this->pdo->prepare("UPDATE users SET elo = ? WHERE id = ?");
        return $stmt->execute([$newElo, $userId]);
    }

     
    public function getTopRankings($limit = 50) {
        $stmt = $this->pdo->prepare("SELECT id, username, nickname, elo, avatar 
                                     FROM users 
                                     ORDER BY elo DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function searchUsers($query, $excludeUserId, $limit = 5) {
        $stmt = $this->pdo->prepare("SELECT id, nickname, username, avatar, elo 
                                     FROM users 
                                     WHERE (nickname LIKE ? OR username LIKE ?) 
                                     AND id != ? LIMIT ?");
        $stmt->bindValue(1, "%$query%", PDO::PARAM_STR);
        $stmt->bindValue(2, "%$query%", PDO::PARAM_STR);
        $stmt->bindValue(3, $excludeUserId, PDO::PARAM_INT);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function findOpponent($opponentName) {
        $stmt = $this->pdo->prepare("SELECT id, elo, avatar FROM users WHERE username = ? OR nickname = ? LIMIT 1");
        $stmt->execute([$opponentName, $opponentName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
