<?php
session_start();
require_once 'config.php';
require_once 'BLL/UserBLL.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$message = "";

$userBLL = new UserBLL($pdo);
$currentUser = $userBLL->getUserBasicInfo($user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nickname']) && !empty($_POST['nickname'])) {
        $result = $userBLL->updateNickname($user_id, $_POST['nickname']);
        $message = $result['message'];
        if ($result['success']) {
            $_SESSION['nickname'] = trim($_POST['nickname']);
        }
        $error = isset($result['error']) ? $result['error'] : false;
    }

    // 2. XỬ LÝ UPLOAD AVATAR
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $result = $userBLL->updateAvatar($user_id, $_FILES['avatar']);
        $message = $result['message'];
        if ($result['success']) {
            $_SESSION['avatar'] = $result['path'];
        }
    }
}


$user = $userBLL->getUserById($user_id);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Cá Nhân</title>
    <link rel="stylesheet" href="sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #363532; color: white; display: flex; margin: 0; }
        .sidebar { position: fixed; top: 0; left: 0; width: 230px; height: 100vh; background-color: #262421; }
        .main-content { margin-left: 0; padding: 40px; flex: 1; display: flex; justify-content: center; }
        .profile-card { background: #262421; padding: 40px; border-radius: 15px; width: 100%; max-width: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); border: 1px solid #3d3b39; }
        .avatar-preview { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #81b64c; margin-bottom: 20px; background-color: #312e2b; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="profile-card text-center">
            <h2 class="mb-4" style="color: #81b64c;">Quản Lý Hồ Sơ</h2>
            <?php if($message): ?> <div class="alert alert-success border-0 bg-success text-white"><?php echo $message; ?></div> <?php endif; ?>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <img src="<?php echo $user['avatar'] ?: 'default_avatar.png'; ?>" class="avatar-preview" id="previewImg">
                
                <div class="mb-4 text-start">
                    <label class="form-label">Thay đổi ảnh đại diện</label>
                    <input type="file" name="avatar" class="form-control bg-dark text-white border-secondary" onchange="previewFile()">
                </div>

              

                <div class="mb-4 text-start">
                    <label class="form-label">Tên hiển thị (Nickname)</label>
                    <input type="text" name="nickname" class="form-control bg-dark text-white border-secondary" 
                           value="<?php echo htmlspecialchars($user['nickname'] ?: $user['username']); ?>" required>
                </div>

                <button type="submit" class="btn w-100 fw-bold py-2" style="background-color: #81b64c; border: 2px solid #81b64c; color: white;">LƯU THAY ĐỔI</button>
                <div class="mt-3">
                    <a href="homepage.php" class="text-decoration-none text-muted small">← Quay lại trang chủ</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewFile() {
            const preview = document.getElementById('previewImg');
            const file = document.querySelector('input[type=file]').files[0];
            const reader = new FileReader();
            reader.onloadend = function() { preview.src = reader.result; }
            if (file) reader.readAsDataURL(file);
        }
    </script>
</body>
</html>