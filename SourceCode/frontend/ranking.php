<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';
require_once '../bll/UserBLL.php';

$user_id = $_SESSION['user_id'];
$userBLL = new UserBLL($pdo);
$currentUser = $userBLL->getUserBasicInfo($user_id);
$rankings = $userBLL->getRankings(50);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng Xếp Hạng - Chess Online</title>
    <link rel="stylesheet" href="sidebar.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #363532; color: white; margin: 0; display: flex; }
        main { flex: 1; display: flex; justify-content: center; align-items: center; gap: 40px; padding: 20px; }
        .main-content { flex: 1; margin-left: 0; padding: 40px; display: flex; flex-direction: column; align-items: center; }
        .ranking-card { width: 100%; max-width: 900px; background-color: #262421; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); }
        .ranking-table { width: 100%; color: #e0e0e0; border-collapse: separate; border-spacing: 0 10px; table-layout: fixed; }
        .col-rank { width: 15%; } .col-player { width: 60%; } .col-elo { width: 25%; }
        .ranking-table th { text-align: center; color: #888; font-size: 0.9rem; padding-bottom: 10px; text-transform: uppercase; }
        .ranking-table th.text-player { text-align: left; padding-left: 20px; }
        .ranking-table tr { background-color: #2e2c29; transition: 0.3s; }
        .ranking-table td { padding: 15px; vertical-align: middle; border: none; }
        .ranking-table td:first-child { border-radius: 10px 0 0 10px; font-weight: 800; font-size: 1.8rem; text-align: center; font-family: 'Arial Black', sans-serif; }
        .ranking-table td:last-child { border-radius: 0 10px 10px 0; color: #81b64c; font-weight: bold; font-size: 1.1rem; text-align: center; }
        .player-info { display: flex; align-items: center; gap: 15px; padding-left: 5px; }
        .rank-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #81b64c; }
        .rank-1 { color: #ffd700; text-shadow: 0 0 10px rgba(255, 215, 0, 0.3); }
        .rank-2 { color: #c0c0c0; }
        .rank-3 { color: #cd7f32; }
        .rank-normal { color: #888; font-size: 1.4rem !important; }
        .my-rank { border: 2px solid #81b64c !important; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="ranking-card">
            <h2 class="mb-4 text-center"><i class="bi bi-trophy-fill text-warning"></i> BẢNG XẾP HẠNG</h2>
            <table class="ranking-table">
                <thead>
                    <tr style="background: transparent;">
                        <th>HẠNG</th>
                        <th style="text-align: left; padding-left: 60px;">NGƯỜI CHƠI</th>
                        <th>ELO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($rankings as $row):
                        $is_me = ($row['id'] == $user_id);
                        $rank_class = "";
                        if ($rank == 1) { $rank_class = "rank-1"; }
                        elseif ($rank == 2) { $rank_class = "rank-2"; }
                        elseif ($rank == 3) { $rank_class = "rank-3"; }
                        else { $rank_class = "rank-normal"; }
                    ?>
                        <tr class="<?php echo $is_me ? 'my-rank' : ''; ?>">
                            <td class="<?php echo $rank_class; ?>"><?php echo $rank; ?></td>
                            <td>
                                <div class="player-info">
                                    <img src="<?php echo $row['avatar'] ?: 'default_avatar.png'; ?>" class="rank-avatar">
                                    <div style="overflow: hidden;">
                                        <div class="fw-bold text-truncate"><?php echo htmlspecialchars($row['nickname'] ?: $row['username']); ?></div>
                                        <?php if ($row['nickname']): ?>
                                            <small class="text-muted text-truncate d-block">@<?php echo htmlspecialchars($row['username']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($is_me): ?>
                                        <span class="badge bg-success ms-auto mr-2">Bạn</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo $row['elo']; ?></td>
                        </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
