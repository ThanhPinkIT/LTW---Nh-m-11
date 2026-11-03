<?php
session_start();
// Xóa toàn bộ dữ liệu Session
$_SESSION = [];
session_unset();
session_destroy();
// Chuyển về trang chủ
header("Location: index.php");
exit();
?>