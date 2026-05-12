<?php
class FriendDAL {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function sendRequest($senderId, $receiverId) {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)");
        return $stmt->execute([$senderId, $receiverId]);
    }
    public function getFriends($userId) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.nickname, u.username, u.avatar, u.elo 
            FROM friend_requests fr
            JOIN users u ON (u.id = CASE WHEN fr.sender_id = ? THEN fr.receiver_id ELSE fr.sender_id END)
            WHERE (fr.sender_id = ? OR fr.receiver_id = ?) 
            AND fr.status = 'accepted'
        ");
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingRequests($userId) {
        $stmt = $this->pdo->prepare("SELECT fr.id as request_id, u.nickname, u.username, u.avatar 
                                     FROM friend_requests fr 
                                     JOIN users u ON fr.sender_id = u.id 
                                     WHERE fr.receiver_id = ? AND fr.status = 'pending'");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function acceptRequest($requestId, $receiverId) {
        $stmt = $this->pdo->prepare("UPDATE friend_requests SET status = 'accepted' WHERE id = ? AND receiver_id = ?");
        return $stmt->execute([$requestId, $receiverId]);
    }

    public function rejectRequest($requestId, $receiverId) {
        $stmt = $this->pdo->prepare("DELETE FROM friend_requests WHERE id = ? AND receiver_id = ?");
        return $stmt->execute([$requestId, $receiverId]);
    }


    public function unfriend($userId, $friendId) {
        $stmt = $this->pdo->prepare("DELETE FROM friend_requests 
                                     WHERE ((sender_id = ? AND receiver_id = ?) 
                                     OR (sender_id = ? AND receiver_id = ?)) 
                                     AND status = 'accepted'");
        return $stmt->execute([$userId, $friendId, $friendId, $userId]);
    }
}
?>
