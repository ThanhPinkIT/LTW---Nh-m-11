<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Xóa session
$_SESSION = [];
session_unset();
session_destroy();

// Quay về trang chủ FE
header("Location: index.php");
exit();
?>

