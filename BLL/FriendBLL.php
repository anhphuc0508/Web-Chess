<?php
require_once __DIR__ . '/../DAL/FriendDAL.php';

class FriendBLL {
    private $friendDAL;

    public function __construct($pdo) {
        $this->friendDAL = new FriendDAL($pdo);
    }

    public function sendFriendRequest($senderId, $receiverId) {
        if ($receiverId <= 0 || $senderId == $receiverId) {
            return ['success' => false];
        }
        $result = $this->friendDAL->sendRequest($senderId, $receiverId);
        return ['success' => $result];
    }

    public function getFriends($userId) {
        return $this->friendDAL->getFriends($userId);
    }


    public function getPendingRequests($userId) {
        return $this->friendDAL->getPendingRequests($userId);
    }

    public function handleRequest($userId, $requestId, $action) {
        if ($requestId <= 0) {
            return ['success' => false];
        }

        if ($action === 'accept') {
            $result = $this->friendDAL->acceptRequest($requestId, $userId);
        } else {
            $result = $this->friendDAL->rejectRequest($requestId, $userId);
        }

        return ['success' => $result];
    }


    public function unfriend($userId, $friendId) {
        if ($friendId <= 0) {
            return ['success' => false];
        }
        $result = $this->friendDAL->unfriend($userId, $friendId);
        return ['success' => $result];
    }
}
?>
