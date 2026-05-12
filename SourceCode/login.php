<?php
session_start();

// Nếu đã đăng nhập rồi thì chuyển hướng vào trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit();
}

$error = '';

// Gọi file cấu hình database vào đây
require_once 'config.php';
require_once 'BLL/UserBLL.php';

// XỬ LÝ KHI NGƯỜI DÙNG BẤM "VÀO CHƠI"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $userBLL = new UserBLL($pdo);
    $result = $userBLL->login($username, $password);

    if ($result['success']) {
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['username'] = $result['user']['username'];
        $_SESSION['nickname'] = $result['user']['nickname'];
        $_SESSION['avatar'] = $result['user']['avatar'];
        $_SESSION['elo'] = $result['user']['elo'];

        header("Location: homepage.php");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Chess Online</title>
    <style>
        body {
            font-family: -apple-system, system-ui, sans-serif;
            background-color: #312e2b;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background-color: #262421;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            width: 320px;
            text-align: center;
        }

        h2 {
            color: #81b64c;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #3d3b39;
            background-color: #312e2b;
            color: white;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #81b64c;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #81b64c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            margin-top: 15px;
            transition: 0.2s;
        }

        button:hover {
            background-color: #a3d160;
        }

        .error {
            color: #f24e4e;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .register-link {
            margin-top: 20px;
            font-size: 14px;
            color: #c3c2c1;
        }

        .register-link a {
            color: #81b64c;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>♚ Chess Login</h2>

        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST" action="">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng Nhập</button>
        </form>

        <div class="register-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </div>
</body>

</html>