<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';
require_once '../bll/MatchBLL.php';
require_once '../bll/UserBLL.php';

$user_id = $_SESSION['user_id'];
$matchBLL = new MatchBLL($pdo);
$userBLL = new UserBLL($pdo);
$currentUser = $userBLL->getUserBasicInfo($user_id);
$history_list = $matchBLL->getAllHistory($user_id);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Thi Đấu - Chess Online</title>
    <link rel="stylesheet" href="sidebar.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #363532; color: white; margin: 0; overflow: hidden; height: 100vh; }
        main { flex: 1; display: flex; justify-content: center; align-items: center; gap: 40px; padding: 20px; }
        .main-content { flex: 1; margin-left: 0; padding: 40px; display: flex; flex-direction: column; align-items: center; height: 100vh; box-sizing: border-box; }
        .history-card { width: 100%; max-width: 900px; background-color: #262421; border-radius: 15px; padding: 30px 30px 10px 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); display: flex; flex-direction: column; max-height: 100%; }
        .table-wrapper { flex: 1; overflow-y: auto; padding-right: 10px; }
        .table-wrapper::-webkit-scrollbar { width: 8px; }
        .table-wrapper::-webkit-scrollbar-track { background: #1e1e1e; border-radius: 4px; }
        .table-wrapper::-webkit-scrollbar-thumb { background: #3d3b39; border-radius: 4px; }
        .table-wrapper::-webkit-scrollbar-thumb:hover { background: #81b64c; }
        .history-table { width: 100%; color: #e0e0e0; border-collapse: separate; border-spacing: 0 10px; table-layout: fixed; }
        .history-table thead th { position: sticky; top: 0; background-color: #262421; z-index: 10; border-bottom: 2px solid #81b64c !important; }
        .history-table tr { background-color: #2e2c29; }
        .history-table td, .history-table th { padding: 15px; text-align: center; vertical-align: middle; border: none; }
        .history-table td:first-child { border-radius: 10px 0 0 10px; text-align: left; padding-left: 25px; }
        .history-table td:last-child { border-radius: 0 10px 10px 0; color: #888; font-size: 0.9rem; }
        .win-text { color: #81b64c; font-weight: bold; }
        .lose-text { color: #e63f3f; font-weight: bold; }
        .draw-text { color: #888; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="history-card">
            <h2 class="mb-4 text-center"><i class="bi bi-clock-history" style="color:#81b64c"></i> LỊCH SỬ THI ĐẤU</h2>
            <div class="table-wrapper">
                <table class="history-table">
                    <thead>
                        <tr style="background: transparent;">
                            <th style="text-align: left; padding-left: 25px;">ĐỐI THỦ</th>
                            <th>CHẾ ĐỘ</th>
                            <th>KẾT QUẢ</th>
                            <th>NƯỚC ĐI</th>
                            <th>THỜI GIAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history_list)): ?>
                            <tr><td colspan="5" class="text-center">Chưa có dữ liệu thi đấu !</td></tr>
                        <?php else: ?>
                            <?php foreach ($history_list as $row): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($row['opponent_name']); ?></td>
                                    <td><span class="badge bg-dark"><?php echo $row['game_mode'] == 'bot_mode' ? 'Đấu với Máy' : 'Trực Tuyến'; ?></span></td>
                                    <td><?php
                                        if ($row['result'] == 'win') echo '<span class="win-text">Thắng</span>';
                                        elseif ($row['result'] == 'lose') echo '<span class="lose-text">Thua</span>';
                                        else echo '<span class="draw-text">Hòa</span>';
                                    ?></td>
                                    <td><?php echo $row['total_moves']; ?> nước</td>
                                    <td><?php echo date('d/m/H:i', strtotime($row['played_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
