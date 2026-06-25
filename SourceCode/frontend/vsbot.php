<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';
require_once '../bll/UserBLL.php';

$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
if ($level < 1) $level = 1;
if ($level > 10) $level = 10;
$botName = "Bot Cấp " . $level;

$userBLL = new UserBLL($pdo);
$currentUser = $userBLL->getUserBasicInfo($_SESSION['user_id']);
$user_data = $userBLL->getUserDisplayInfo($_SESSION['user_id']);
$playerAvatar = $user_data['avatar'] ?: 'default_avatar.png';
$displayName = $user_data['nickname'] ?: $user_data['username'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chơi với <?php echo $botName; ?></title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="../assets/css/chessboard-1.0.0.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #302e2b !important;
            color: #ffffff !important;
        }

        main {
            flex: 1;
            margin-left: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            padding: 20px;
        }

        .board-area {
            width: 500px;
        }

        .info-panel {
            background: #262421;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            height: 600px;
            display: flex;
            flex-direction: column;
        }

        #move-history {
            background-color: #1e1e1e;
            height: 400px;
            overflow-y: auto;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            color: #ccc;
            flex: 1;
           
        }

        .btn-custom {
            background-color: #3a3936;
            color: white;
            border: none;
            padding: 12px 25px;
            transition: 0.2s;
            box-shadow: 4px 4px 0px #201f1f;
        }

        .btn-custom:hover {
            background-color: #4a4946;
            transform: translateY(1px);
            box-shadow: 2px 2px 0px #201f1f;
        }

        .black-3c85d {
            background-color: #769656 !important;
        }

        .white-1e1d7 {
            background-color: #eeeed2 !important;
        }

        .highlight-selected {
            background-color: rgba(186, 202, 68, 0.5) !important;
        }

        .highlight-move {
            position: relative;
            cursor: pointer;
        }

        .highlight-move::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 25%;
            height: 25%;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .highlight-capture {
            position: relative;
            cursor: pointer;
        }

        .highlight-capture::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            border: 6px solid rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            pointer-events: none;
        }

        .player-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
            padding: 8px 15px;
            background-color: #262421;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #3d3b39;
        }

        .player-info.bottom-player {
            margin-bottom: 0;
            margin-top: 12px;
        }

        .avatar-box {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .player-text {
            display: flex;
            flex-direction: column;
        }

        .player-text .name {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
        }

        .player-text .elo {
            font-size: 13px;
            color: #c3c2c1;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <main>
        <div class="board-area">
            <div class="player-info">
                <div class="avatar-box" style="background-color: #5c5854;">🤖</div>
                <div class="player-text">
                    <div class="name"><?php echo $botName; ?></div>
                    <div class="elo" style="color: #81b64c; font-weight: bold;">LEVEL <?php echo $level; ?></div>
                </div>
            </div>
            <div id="mainBoard"></div>
            <div class="player-info bottom-player">
                <img src="<?php echo $playerAvatar; ?>" style="width: 45px; height: 45px; border-radius: 8px; border: 2px solid #81b64c; object-fit: cover; box-shadow: 0 2px 5px rgba(0,0,0,0.5);">
                <div class="player-text">
                    <div class="name"><?php echo htmlspecialchars($displayName); ?></div>
                    <div class="elo">⭐ <?php echo isset($_SESSION['elo']) ? htmlspecialchars($_SESSION['elo']) : "1200"; ?></div>
                </div>
            </div>
        </div>
        <div class="info-panel">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #fff;">Lịch sử nước đi</h3>
            <div class="history-header" style="display: flex; border-bottom: 2px solid #81b64c; padding-bottom: 5px; margin-bottom: 5px; font-weight: bold; color: #c3c2c1; font-size: 14px;">
                <span style="width: 20%;">#</span>
                <span style="width: 40%;">Trắng</span>
                <span style="width: 40%;">Đen</span>
            </div>
            <div id="move-history"></div>
            <div id="status" class="mt-2 mb-2" style="color: #81b64c; font-weight: bold;">Đến lượt bạn</div>
            <div class="d-flex justify-content-center gap-3 mt-auto">
                <button id="btn-resign" class="btn-custom" title="Đầu hàng"><i class="bi bi-flag"></i></button>
                <button id="btn-undo" class="btn-custom" title="Quay lại"><i class="bi bi-arrow-return-left"></i></button>
                <button onclick="location.reload()" class="btn-custom" title="Chơi lại"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
        </div>
    </main>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/chess.min.js"></script>
    <script src="../assets/js/chessboard-1.0.0.min.js"></script>
    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
    <script>
        const game = new Chess();
        const myColor = 'w';
        const level = <?php echo $level; ?>;
        const skillLevel = (level - 1) * 2;
        var board = null;
        var selectedSquare = null;
        const stockfish = new Worker('../assets/js/stockfish.js');
        stockfish.onmessage = function(event) {
            if (event.data.indexOf('bestmove') > -1) {
                const move = event.data.split(' ')[1];
                game.move({
                    from: move.substring(0, 2),
                    to: move.substring(2, 4),
                    promotion: 'q'
                });
                board.position(game.fen());
                updateUI();
            }
        };

        function makeBotMove() {
            if (game.game_over()) return;
            stockfish.postMessage(`setoption name Skill Level value ${skillLevel}`);
            stockfish.postMessage(`position fen ${game.fen()}`);
            stockfish.postMessage(`go movetime ${level * 100}`);
        }

        function removeHighlights() {
            $('#mainBoard .square-55d63').removeClass('highlight-selected highlight-move highlight-capture');
        }

        function highlightValidMoves(square) {
            removeHighlights();
            var moves = game.moves({
                square: square,
                verbose: true
            });
            if (moves.length === 0) return;
            $('#mainBoard .square-' + square).addClass('highlight-selected');
            for (var i = 0; i < moves.length; i++) {
                var tgt = moves[i].to;
                if (game.get(tgt)) {
                    $('#mainBoard .square-' + tgt).addClass('highlight-capture');
                } else {
                    $('#mainBoard .square-' + tgt).addClass('highlight-move');
                }
            }
        }
        document.getElementById('mainBoard').addEventListener('mousedown', function(e) {
            if (game.game_over()) return;
            if (game.turn() !== myColor) return;
            var squareEl = e.target.closest('.square-55d63');
            if (!squareEl) return;
            var square = squareEl.getAttribute('data-square');
            if (!square) return;
            var piece = game.get(square);
            if (selectedSquare) {
                var moves = game.moves({
                    square: selectedSquare,
                    verbose: true
                });
                var isValidMove = moves.find(m => m.to === square);
                if (isValidMove) {
                    game.move({
                        from: selectedSquare,
                        to: square,
                        promotion: 'q'
                    });
                    board.position(game.fen());
                    removeHighlights();
                    selectedSquare = null;
                    updateUI();
                    if (!game.game_over()) {
                        $('#status').text("Máy đang nghĩ...");
                        window.setTimeout(makeBotMove, 250);
                    }
                    e.stopPropagation();
                    e.preventDefault();
                    return;
                }
                if (piece && piece.color === myColor) {
                    selectedSquare = square;
                    highlightValidMoves(square);
                    return;
                }
                removeHighlights();
                selectedSquare = null;
                return;
            }
            if (piece && piece.color === myColor) {
                selectedSquare = square;
                highlightValidMoves(square);
            }
        }, true);

        function updateUI() {
            removeHighlights();
            selectedSquare = null;
            const history = game.history();
            const container = $('#move-history').empty();
            for (let i = 0; i < history.length; i += 2) {
                let moveNum = (i/2) + 1;
                let whiteMove = history[i];
                let blackMove = history[i+1] || '';
                container.append(`
                <div style="display: flex; padding: 4px 0; border-bottom: 1px solid #3d3b39;">
                    <span style="width: 20%; color: #888;">${moveNum}.</span>
                    <span style="width: 40%; color: #fff;">${whiteMove}</span>
                    <span style="width: 40%; color: #fff;">${blackMove}</span>
                </div>`);
            }
            container.scrollTop(container[0].scrollHeight);
            if (game.game_over()) {
                let res = game.in_checkmate() ? (game.turn() === 'w' ? 'lose' : 'win') : 'draw';
                $.post('../api/save_match.php', {
                    opponent_name: '<?php echo $botName; ?>',
                    game_mode: 'bot_mode',
                    result: res,
                    total_moves: Math.ceil(game.history().length / 2)
                }, () => {
                    alert("Kết thúc! Bạn " + (res === 'win' ? "Thắng" : (res === 'lose' ? "Thua" : "Hòa")));
                    window.location.href = 'homepage.php';
                });
            } else {
                $('#status').text(game.turn() === 'w' ? "Đến lượt bạn" : "Đến lượt máy");
            }
        }
        board = Chessboard('mainBoard', {
            draggable: false,
            position: 'start',
            pieceTheme: '../assets/img/{piece}.png'
        });
        stockfish.postMessage('uci');
        $('#btn-undo').click(() => {
            if (game.history().length >= 2) {
                game.undo();
                game.undo();
                board.position(game.fen());
                updateUI();
            }
        });
        $('#btn-resign').click(() => {
            if (confirm("Xác nhận đầu hàng?")) {
                $.post('../api/save_match.php', {
                    opponent_name: '<?php echo $botName; ?>',
                    game_mode: 'bot_mode',
                    result: 'lose',
                    total_moves: Math.ceil(game.history().length / 2)
                }, () => window.location.href = 'homepage.php');
            }
        });
    </script>
</body>

</html>
