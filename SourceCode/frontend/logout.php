<?php
session_start();
session_unset();    // Xóa sạch các biến trong session
session_destroy();  // Hủy bỏ hoàn toàn phiên làm việc

// Đuổi người dùng về trang đăng nhập
header("Location: index.php");
exit();
?>
