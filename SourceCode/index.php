<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chơi Cờ Vua Trực Tuyến</title>
    
    <link rel="stylesheet" href="./assets/css/chessboard-1.0.0.min.css">
    
    <style>
        body {
            font-family: -apple-system, system-ui, sans-serif; background-color: #312e2b;
            color: white; margin: 0; display: flex; flex-direction: column; height: 100vh;
        }

        header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 50px; background-color: #262421; border-bottom: 1px solid #3d3b39;
        }
        .logo { font-size: 28px; font-weight: bold; color: #81b64c; text-decoration: none; }
        
        .auth-buttons a {
            text-decoration: none; font-weight: bold; padding: 12px 24px; 
            border-radius: 8px; margin-left: 15px; transition: 0.2s; font-size: 16px;
        }
        .btn-login { color: #c3c2c1; background-color: #3d3b39; }
        .btn-login:hover { background-color: #4c4a48; color: white; }
        
        .btn-register { color: white; background-color: #81b64c; box-shadow: 0 4px 0 #537133; }
        .btn-register:hover { background-color: #a3d160; transform: translateY(2px); box-shadow: 0 2px 0 #537133; }

        .hero {
            flex: 1; display: flex; justify-content: center; align-items: center; gap: 80px; padding: 50px;
        }
        .hero-text { max-width: 550px; }
        .hero-text h1 { font-size: 56px; margin-bottom: 20px; line-height: 1.2; }
        .hero-text p { font-size: 18px; color: #c3c2c1; margin-bottom: 40px; line-height: 1.6; }

        /* Khu vực chứa bàn cờ thật */
        .board-container {
            width: 450px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); border-radius: 4px; pointer-events: none; /* Khóa click chuột vào bàn cờ */
        }

        .play-buttons { display: flex; flex-direction: column; gap: 20px; }
        .play-btn {
            display: flex; justify-content: center; align-items: center; gap: 20px; padding: 20px 30px;
            border-radius: 10px; text-decoration: none; color: white; font-size: 28px; font-weight: bold;
            transition: 0.2s; width: 380px;
        }
        
        .play-online { background-color: #81b64c; box-shadow: 0 8px 0 #537133; }
        .play-online:hover { background-color: #a3d160; transform: translateY(4px); box-shadow: 0 4px 0 #537133; }

        .play-computer { background-color: #3d3b39; box-shadow: 0 8px 0 #201e1b; }
        .play-computer:hover { background-color: #4c4a48; transform: translateY(4px); box-shadow: 0 4px 0 #201e1b; }
        .white-1e1d7 {
            background-color: #eeeed2 !important;
            color: #769656 !important;
        }

        .black-3c85d {
            background-color: #769656 !important;
            color: #eeeed2 !important;
        }

        .board-b72b1 {
            border: 2px solid #3d3b39 !important;
            border-radius: 5px !important;
            overflow: hidden !important;
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">♚ CHESS</a>
        <div class="auth-buttons">
            <a href="login.php" class="btn-login">Đăng nhập</a>
            <a href="register.php" class="btn-register">Đăng ký</a>
        </div>
    </header>

    <main class="hero">
        <div class="board-container">
            <div id="myBoard" style="width: 100%"></div>
        </div>

        <div class="hero-text">
            <h1>Chơi Cờ Vua Trực Tuyến &amp; Miễn Phí!</h1>
            <p>Tham gia cùng hàng triệu người chơi khác ngay ngày hôm nay.</p>
            
            <div class="play-buttons">
                <a href="login.php" class="play-btn play-online">
                    <span></span> Chơi Trực Tuyến
                </a>
                <a href="login.php" class="play-btn play-computer">
                    <span></span> Chơi Với Máy
                </a>
            </div>
        </div>
    </main>

    <script src="./assets/js/jquery-3.6.0.min.js"></script>
    <script src="./assets/js/chessboard-1.0.0.min.js"></script>
    <script src="./assets/js/chess.min.js"></script>

    <script>
        var board = null;
        var game = new Chess();

        function makeRandomMove() {
            // Nếu ván cờ kết thúc (Hòa hoặc Chiếu bí), reset lại từ đầu sau 2 giây
            if (game.game_over()) {
                window.setTimeout(function() {
                    game.reset();
                    board.start();
                    makeRandomMove();
                }, 2000);
                return;
            }

            var possibleMoves = game.moves();
            
            // Chọn bừa một nước đi hợp lệ
            var randomIdx = Math.floor(Math.random() * possibleMoves.length);
            game.move(possibleMoves[randomIdx]);
            
            // Cập nhật lại giao diện bàn cờ
            board.position(game.fen());

            // Vòng lặp: Máy tự gọi lại hàm này sau 500 mili-giây (0.5s)
            window.setTimeout(makeRandomMove, 500);
        }

        var config = {
            draggable: false, // KHÔNG CHO NGƯỜI DÙNG KÉO THẢ
            position: 'start',
            pieceTheme: './assets/img/{piece}.png'
        };

        board = Chessboard('myBoard', config);

        // Khởi động vòng lặp tự đánh lần đầu tiên
        window.setTimeout(makeRandomMove, 500);
    </script>
</body>
</html>