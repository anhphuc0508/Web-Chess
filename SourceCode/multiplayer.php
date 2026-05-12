<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';
require_once 'BLL/UserBLL.php';

$user_id = $_SESSION['user_id'];
$userBLL = new UserBLL($pdo);
$currentUser = $userBLL->getUserBasicInfo($user_id);

$matchId = $_GET['id'] ?? 'room_default';
$playerColor = $_GET['color'] ?? 'w';
$opponentName = $_GET['opponent'] ?? 'Đối thủ';
$opponentElo = $_GET['elo'] ?? '1200';

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chess Online PvP</title>
    <link rel="stylesheet" href="sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./assets/css/chessboard-1.0.0.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #302e2b !important;
            color: #ffffff !important;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            padding: 20px;
        }

        .board-area {
            width: 500px;
        }

        .white-1e1d7 {
            background-color: #eeeed2 !important;
        }

        .black-3c85d {
            background-color: #769656 !important;
        }

        #status {
            color: #81b64c;
            font-weight: bold;
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

        .info-panel {
            color: white;
            display: flex;
            flex-direction: column;
            background: #262421;
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            height: 500px;
        }

        #move-history {
            background-color: #1e1e1e;
            height: 350px;
            overflow-y: auto;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 14px;
            color: #ccc;
            flex: 1;
        }

        .history-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #333;
            padding: 4px 0;
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
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <main>
        <div class="board-area">
            <div class="player-info">
                <div class="avatar-box" style="background-color: #5c5854;">👽</div>
                <div class="player-text">
                    <div class="name" id="opponent-name"><?php echo htmlspecialchars($opponentName); ?></div>
                    <div class="elo">⭐ <?php echo htmlspecialchars($opponentElo); ?></div>
                </div>
            </div>

            <div id="mainBoard"></div>

            <div class="player-info bottom-player">
                <div class="avatar-box" style="background-color: #81b64c; border: 2px solid #fff;">😎</div>
                <div class="player-text">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div class="elo">⭐ <?php echo isset($_SESSION['elo']) ? htmlspecialchars($_SESSION['elo']) : "1200"; ?></div>
                </div>
            </div>
        </div>

        <div class="info-panel">
            <h2 style="font-size: 20px; color: #fff; margin-top: 0;">Phòng: <?php echo htmlspecialchars($matchId); ?></h2>
            <p style="margin-bottom: 5px;">Bạn cầm quân: <strong style="color: #81b64c;"><?php echo $playerColor == 'w' ? 'Trắng' : 'Đen'; ?></strong></p>
            <p style="margin-bottom: 10px;">Trạng thái: <span id="status">Đang kết nối...</span></p>
            <hr style="border: 1px solid #3d3b39; margin : 10px 0;">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #fff;">Lịch sử nước đi</h3>
            <div class="history-header" style="display: flex; border-bottom: 2px solid #81b64c; padding-bottom: 5px; margin-bottom: 5px; font-weight: bold; color: #c3c2c1; font-size: 14px;">
                <span style="width: 20%;">#</span>
                <span style="width: 40%;">Trắng</span>
                <span style="width: 40%;">Đen</span>
            </div>
            <div id="move-history"></div>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <button id="btn-resign" class="btn-custom" type="button" title="Đầu hàng">
                    <i class="bi bi-flag"></i>
                </button>
                <button id="btn-draw" class="btn-custom" type="button" title="Cầu hòa">
                    <i class="bi bi-signpost-split"></i>
                </button>
            </div>
        </div>

        <div class="modal fade" id="drawOfferModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background-color: #262421; color: white; border: 1px solid #81b64c;">
                    <div class="modal-header" style="border-bottom: 1px solid #3d3b39;">
                        <h5 class="modal-title" style="color: #81b64c; font-weight: bold;">Yêu cầu hòa</h5>
                    </div>
                    <div class="modal-body" style="font-size: 16px;">
                        Đối thủ yêu cầu hòa cờ. Bạn có đồng ý không?
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #3d3b39;">
                        <button type="button" class="btn btn-danger" id="btn-decline-draw" data-bs-dismiss="modal">Từ chối</button>
                        <button type="button" class="btn btn-success" id="btn-accept-draw" data-bs-dismiss="modal">Đồng ý hòa</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="sendDrawModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background-color: #262421; color: white; border: 1px solid #81b64c;">
                    <div class="modal-header" style="border-bottom: 1px solid #3d3b39;">
                        <h5 class="modal-title" style="color: #81b64c; font-weight: bold;">Xác nhận cầu hòa</h5>
                    </div>
                    <div class="modal-body" style="font-size: 16px;">
                        Bạn có chắc chắn muốn gửi lời cầu hòa đến đối thủ?
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #3d3b39;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" id="btn-confirm-send-draw" data-bs-dismiss="modal">Gửi yêu cầu</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="./assets/js/jquery-3.6.0.min.js"></script>
    <script src="./assets/js/chess.min.js"></script>
    <script src="./assets/js/chessboard-1.0.0.min.js"></script>
    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>

    <script>
        const matchId = "<?php echo $matchId; ?>";
        const myColor = "<?php echo $playerColor; ?>";
        const socket = io('http://localhost:3000');
        const game = new Chess();
        var board = null;
        var selectedSquare = null; 

        socket.emit('join-room', matchId);

        function saveOnlineMatch(resultStatus) {
            let totalMoves = Math.ceil(game.history().length / 2);
            $.ajax({
                url: 'API/save_match.php',
                type: 'POST',
                data: {
                    opponent_name: '<?php echo $opponentName; ?>',
                    game_mode: 'online_mode',
                    result: resultStatus,
                    total_moves: totalMoves,
                    color: myColor
                },
                success: function() {
                    window.location.href = 'homepage.php';
                }
            });
        }

        function checkGameOver() {
            if (game.game_over()) {
                let result = 'draw';
                if (game.in_checkmate()) {
                    result = (game.turn() === myColor) ? 'lose' : 'win';
                }
                socket.emit('match-finished', matchId);
                alert("Ván cờ kết thúc! Bạn " + (result === 'win' ? "THẮNG" : (result === 'lose' ? "THUA" : "HÒA")));
                saveOnlineMatch(result);
            }
        }

        $('#btn-draw').on('click', function() {
            var sendModal = new bootstrap.Modal(document.getElementById('sendDrawModal'));
            sendModal.show();
        });
        $('#btn-confirm-send-draw').on('click', function() {
            socket.emit('offer-draw', matchId);
            $('#status').text("Đã gửi yêu cầu hòa, đang chờ phản hồi...").css('color', '#f1c40f');
        });
        socket.on('draw-offered', () => {
            var drawModal = new bootstrap.Modal(document.getElementById('drawOfferModal'));
            drawModal.show();
        });
        $('#btn-accept-draw').on('click', function() {
            socket.emit('accept-draw', matchId);
            alert("Trận đấu kết thúc với kết quả hòa.");
            saveOnlineMatch('draw');
        });
        $('#btn-decline-draw').on('click', function() {
            socket.emit('decline-draw', matchId);
        });
        socket.on('draw-accepted', () => {
            alert("Đối thủ đã đồng ý. Trận đấu kết thúc với kết quả hòa.");
            saveOnlineMatch('draw');
        });
        socket.on('draw-declined', () => {
            alert("Đối thủ đã từ chối yêu cầu hòa. Trận đấu tiếp tục.");
            $('#status').text("Kết nối ổn định.").css('color', '#81b64c');
        });
        $('#btn-resign').on('click', function() {
            if (confirm("Bạn có chắc chắn muốn đầu hàng?")) {
                socket.emit('resign', matchId);
                alert("Bạn đã đầu hàng.");
                saveOnlineMatch('lose');
            }
        });
        socket.on('opponent-resigned', () => {
            alert("Đối thủ đã đầu hàng. Bạn giành chiến thắng!");
            saveOnlineMatch('win');
        });

        let disconnectInterval = null;

        socket.on('sync-board', (serverHistory) => {
            game.reset();
            serverHistory.forEach(moveData => {
                game.move(moveData);
            });
            board.position(game.fen());
            updateMoveHistory();

            if (disconnectInterval) clearInterval(disconnectInterval);
            $('#status').text("Kết nối ổn định.").css('color', '#81b64c');
        });

        socket.on('opponent-disconnected-temp', () => {
            if (!game.game_over()) {
                let timeLeft = 60;
                $('#status').text(`Đối thủ mất kết nối. Đang chờ (${timeLeft}s)...`).css('color', '#e74c3c');

                if (disconnectInterval) clearInterval(disconnectInterval);
                disconnectInterval = setInterval(() => {
                    timeLeft--;
                    if (timeLeft > 0) {
                        $('#status').text(`Đối thủ mất kết nối. Đang chờ (${timeLeft}s)...`);
                    } else {
                        clearInterval(disconnectInterval);
                    }
                }, 1000);
            }
        });

        socket.on('opponent-reconnected', () => {
            if (!game.game_over()) {
                if (disconnectInterval) clearInterval(disconnectInterval);
                $('#status').text("Đối thủ đã kết nối lại. Trận đấu tiếp tục.").css('color', '#81b64c');
            }
        });

        socket.on('opponent-abandoned', () => {
            if (!game.game_over()) {
                if (disconnectInterval) clearInterval(disconnectInterval);
                alert("Đối thủ đã mất kết nối quá 60 giây. Bạn giành chiến thắng.");

                let totalMoves = Math.ceil(game.history().length / 2);
                $.ajax({
                    url: 'API/save_match.php',
                    type: 'POST',
                    data: {
                        opponent_name: '<?php echo $opponentName; ?>',
                        total_moves: totalMoves,
                        is_abandoned: true
                    },
                    success: function() {
                        window.location.href = 'homepage.php';
                    }
                });
            }
        });

        function updateMoveHistory() {
            const historyElement = $('#move-history');
            historyElement.empty();
            const history = game.history();
            for (let i = 0; i < history.length; i += 2) {
                const moveNumber = (i / 2) + 1;
                const whiteMove = history[i];
                const blackMove = history[i + 1] ? history[i + 1] : '';
                const rowHtml = `
                <div class="history-row" style="display: flex; padding: 4px 0; border-bottom: 1px solid #333;">
                    <span style="width: 20%; color: #888;">${moveNumber}</span>
                    <span style="width: 40%; color: #fff;">${whiteMove}</span>
                    <span style="width: 40%; color: #fff;">${blackMove}</span>
                </div>`;
                historyElement.append(rowHtml);
            }
            historyElement.scrollTop(historyElement[0].scrollHeight);
        }

        socket.on('receive-move', (move) => {
            game.move(move);
            board.position(game.fen());
            updateMoveHistory();
            checkGameOver();
        });


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
                    var move = game.move({
                        from: selectedSquare,
                        to: square,
                        promotion: 'q'
                    });
                    board.position(game.fen());
                    removeHighlights();
                    selectedSquare = null;

                    socket.emit('send-move', {
                        matchId: matchId,
                        move: move.san,
                        fen: game.fen()
                    });

                    updateMoveHistory();
                    checkGameOver();

                    
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


        
        const config = {
            draggable: false, // TẮT kéo thả, chỉ cho phép click
            position: 'start',
            orientation: myColor === 'w' ? 'white' : 'black',
            pieceTheme: './assets/img/{piece}.png'
        };
        board = Chessboard('mainBoard', config);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>