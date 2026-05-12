<?php
/**
 * UserBLL - Lớp xử lý logic nghiệp vụ cho User
 * Xử lý: đăng nhập, đăng ký, cập nhật hồ sơ, tìm kiếm, xếp hạng
 */
require_once __DIR__ . '/../DAL/UserDAL.php';

class UserBLL {
    private $userDAL;

    public function __construct($pdo) {
        $this->userDAL = new UserDAL($pdo);
    }

    /**
     * Xử lý đăng nhập
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function login($username, $password) {
        $username = trim($username);

        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!'];
        }

        $user = $this->userDAL->findByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            return ['success' => true, 'message' => '', 'user' => $user];
        }

        return ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng!'];
    }

    /**
     * Xử lý đăng ký
     * @return array ['success' => bool, 'message' => string]
     */
    public function register($username, $password, $confirmPassword) {
        $username = trim($username);

        if (empty($username) || empty($password) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!'];
        }

        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp!'];
        }

        if ($this->userDAL->usernameExists($username)) {
            return ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại! Vui lòng chọn tên khác.'];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $result = $this->userDAL->createUser($username, $hashedPassword);

        if ($result) {
            return ['success' => true, 'message' => 'Đăng ký thành công! Tự động chuyển đến trang Đăng nhập...'];
        }

        return ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'];
    }

    /**
     * Lấy thông tin user theo ID
     */
    public function getUserById($userId) {
        return $this->userDAL->getUserById($userId);
    }

    /**
     * Lấy thông tin cơ bản (username, nickname, elo, avatar)
     */
    public function getUserBasicInfo($userId) {
        return $this->userDAL->getUserBasicInfo($userId);
    }

    /**
     * Lấy thông tin hiển thị (username, nickname, avatar - dùng cho vsbot)
     */
    public function getUserDisplayInfo($userId) {
        return $this->userDAL->getUserDisplayInfo($userId);
    }

    /**
     * Cập nhật nickname
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateNickname($userId, $nickname) {
        $nickname = trim($nickname);

        if (empty($nickname)) {
            return ['success' => false, 'message' => 'Nickname không được để trống!'];
        }

        if ($this->userDAL->nicknameExistsExcluding($nickname, $userId)) {
            return ['success' => false, 'message' => 'Lỗi: Tên đã tồn tại!', 'error' => true];
        }

        $this->userDAL->updateNickname($userId, $nickname);
        return ['success' => true, 'message' => 'Cập nhật nickname thành công!'];
    }

    /**
     * Cập nhật avatar
     * @return array ['success' => bool, 'message' => string, 'path' => string|null]
     */
    public function updateAvatar($userId, $file) {
        if ($file['error'] != 0) {
            return ['success' => false, 'message' => 'Lỗi upload file!'];
        }

        $targetDir = __DIR__ . "/../uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = "avatar_" . $userId . "_" . time() . ".png";
        $targetFile = $targetDir . $fileName;
        $relativePath = "uploads/" . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $this->userDAL->updateAvatar($userId, $relativePath);
            return ['success' => true, 'message' => 'Ảnh đại diện đã được thay đổi!', 'path' => $relativePath];
        }

        return ['success' => false, 'message' => 'Không thể upload ảnh!'];
    }

    /**
     * Lấy bảng xếp hạng
     */
    public function getRankings($limit = 50) {
        return $this->userDAL->getTopRankings($limit);
    }

    /**
     * Tìm kiếm user
     */
    public function searchUsers($query, $excludeUserId) {
        if (strlen($query) < 2) {
            return [];
        }
        return $this->userDAL->searchUsers($query, $excludeUserId);
    }
}
?>
