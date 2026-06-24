<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';
require_once '../bll/UserBLL.php';
require_once '../bll/MatchBLL.php';

$user_id = $_SESSION['user_id'];

$userBLL = new UserBLL($pdo);
$matchBLL = new MatchBLL($pdo);

$currentUser = $userBLL->getUserBasicInfo($user_id);
$display_name = $currentUser['nickname'] ?: $currentUser['username'];
$avatar_path = $currentUser['avatar'] ?: 'default_avatar.png';

$activeMatch = $matchBLL->getActiveMatch($user_id);
$myColor = $activeMatch ? $activeMatch['myColor'] : 'b';

$stats = $matchBLL->getStats($user_id);
$win_rate = $stats['win_rate'];

$history_list = $matchBLL->getRecentHistory($user_id, 5);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang Chủ - Chess Online</title>
    <link rel="stylesheet" href="sidebar.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-image: url(../assets/img/background.png);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
            overflow: hidden !important;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        

        .main-content {
          
            box-sizing: border-box; 
            overflow-y: auto; 
            margin-left: 0; 
            padding-top: 450px;
            padding-bottom: 80px; 
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1; 
        }

        .player-profile {
            width: 320px;
            background-color: #262421;
            border-radius: 15px;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .avatar-main {
            width: 110px;
            height: 110px;
            border: 4px solid #81b64c;
            border-radius: 50%;
            object-fit: cover;
            background-color: #312e2b;
        }

        .stats-board {
            display: flex;
            width: 100%;
            justify-content: space-around;
            background-color: #312e2b;
            padding: 15px 0;
            border-radius: 10px;
            margin-top: 20px;
        }

        .play-options {
            display: flex;
            gap: 30px;
            margin-top: 10px;
        }

        .play-option {
            width: 230px;
            height: 310px;
            background-color: #262421;
            border-radius: 15px;
            text-decoration: none;
            transition: 0.3s;
            overflow: hidden;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .play-option:hover {
            transform: translateY(-8px);
            border-color: #81b64c;
        }

        .option-img-box {
            width: 100%;
            height: 240px;
            background-color: #1a1917;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
            font-weight: bold;
            color: #81b64c;
        }

        .option-text {
            height: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #e0e0e0;
            font-weight: 600;
        }

        .match-history {
            width: 100%;
            max-width: 1100px;
            background-color: #262421;
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
        }

        .history-table {
            width: 100%;
            color: #e0e0e0 !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .history-table thead th {
            
            background-color: #312e2b !important;
            color: #ffffff !important;
            z-index: 10;
            text-align: center;
            padding: 15px 10px !important;
            border-bottom: 2px solid #81b64c !important;
            font-weight: bold !important;
        }

        .history-table td {
            background-color: #262421 !important;
            color: #e0e0e0 !important;
            text-align: center;
            padding: 12px 10px !important;
            border-bottom: 1px solid #3d3b39 !important;
        }

        .win-color {
            color: #81b64c;
        }

        .lose-color {
            color: #e74c3c;
        }

            
        .modal-content {
            background-color: #262421;
            color: white;
            border: 1px solid #3d3b39;
        }

        .modal-header {
            border-bottom: 1px solid #3d3b39;
        }

        .btn-close {
            filter: invert(1);
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1e1e1e;
        }

        ::-webkit-scrollbar-thumb {
            background: #3d3b39;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #81b64c;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <?php if ($activeMatch): ?>
            <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4 w-100" style="max-width: 1100px; background-color: #262421; border: 1px solid #81b64c; color: white;">
                <div><strong style="color: #81b64c;">Bạn đang có ván đấu chưa kết thúc!</strong></div>
                <a href="multiplayer.php?id=<?php echo $activeMatch['id']; ?>&color=<?php echo $myColor; ?>&opponent=<?php echo urlencode($activeMatch['opponent_name']); ?>&elo=<?php echo $activeMatch['opponent_elo']; ?>" class="btn btn-success">Vào lại ngay</a>
            </div>
        <?php endif; ?>

        <div class="d-flex gap-5">
            <div class="player-profile">
                <img src="<?php echo $avatar_path; ?>" class="avatar-main">
                <div class="mt-3 fs-5 fw-bold"><?php echo htmlspecialchars($display_name); ?></div>
                <div class="text-muted" style="font-size: 0.9rem;">@<?php echo htmlspecialchars($currentUser['username']); ?></div>
                <div class="mt-1" style="color:#81b64c">ELO: <?php echo $currentUser['elo'] ?? "1200"; ?></div>
                <div class="stats-board">
                    <div class="text-center">
                        <div><?php echo $stats['total_matches']; ?></div><small>Ván</small>
                    </div>
                    <div class="text-center">
                        <div class="win-color"><?php echo $win_rate; ?>%</div><small>Thắng</small>
                    </div>
                    <div class="text-center">
                        <div class="lose-color"><?php echo $stats['total_loses']; ?></div><small>Thua</small>
                    </div>
                </div>
            </div>

            <div class="play-options">
                <a href="javascript:void(0)" id="btn-find-match" class="play-option">
                    <div class="option-img-box">ONLINE</div>
                    <span class="option-text">Chơi Trực Tuyến</span>
                </a>
                <div class="play-option" data-bs-toggle="modal" data-bs-target="#botModal">
                    <div class="option-img-box" style="color: #c3c2c1;">BOT</div>
                    <span class="option-text">Chơi Với Máy</span>
                </div>
            </div>
        </div>

        <div class="match-history">
            <h3 class="mb-4">Lịch Sử Thi Đấu Gần Đây</h3>
            <table class="table history-table">
                <thead>
                    <tr>
                        <th>Đối thủ</th>
                        <th>Chế độ</th>
                        <th>Kết quả</th>
                        <th>Nước đi</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history_list)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Chưa có dữ liệu thi đấu.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history_list as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['opponent_name']); ?></td>
                                <td><?php echo $row['game_mode'] == 'online_mode' ? 'Trực Tuyến' : 'Máy'; ?></td>
                                <td>
                                    <?php
                                    if ($row['result'] == 'win') echo '<b class="win-color">Thắng</b>';
                                    elseif ($row['result'] == 'lose') echo '<b class="lose-color">Thua</b>';
                                    else echo '<b style="color: #82817d;">Hòa</b>';
                                    ?>
                                </td>
                                <td><?php echo $row['total_moves']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['played_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="matchmaking-ui" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); display: none; justify-content: center; align-items: center; z-index: 9999;">
        <div class="text-center bg-dark p-5 rounded border ">
            <h2 style="color:#81b64c">Đang tìm đối thủ...</h2>
            <div style="border: 4px solid #3d3b39; border-top: 4px solid #81b64c; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 20px auto;"></div>
            <p id="timer" class="fs-4">00:00</p>
            <button onclick="location.reload()" class="btn btn-danger">Hủy tìm kiếm</button>
        </div>
    </div>

    <div class="modal fade" id="botModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chọn Cấp Độ Máy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Chọn cấp độ:</p>
                    <select class="form-select bg-dark text-white border-secondary mb-3" id="botLevel">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <option value="<?php echo $i; ?>">Cấp độ <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn px-5 fw-bold" style="background-color: #81b64c; border: 2px solid #81b64c; color: white;" onclick="startBotGame()">BẮT ĐẦU</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
    <script>
        const socket = io('http://localhost:3000');

        document.getElementById('btn-find-match').addEventListener('click', function() {
            document.getElementById('matchmaking-ui').style.display = 'flex';
            socket.emit('find-match', {
                userId: "<?php echo $user_id; ?>",
                username: "<?php echo $display_name; ?>",
                elo: "<?php echo $currentUser['elo'] ?? 1200; ?>"
            });

            let sec = 0;
            setInterval(() => {
                sec++;
                let m = Math.floor(sec / 60);
                let s = sec % 60;
                document.getElementById('timer').innerText = (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s);
            }, 1000);
        });

        socket.on('match-found', (data) => {
            window.location.href = `multiplayer.php?id=${data.matchId}&color=${data.color}&opponent=${encodeURIComponent(data.opponentName)}&elo=${data.opponentElo}`;
        });

    
        function startBotGame() {
            const level = document.getElementById('botLevel').value;
            window.location.href = `vsbot.php?level=${level}`;
        }
    </script>
</body>

</html>
