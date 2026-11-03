<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web_ban_hang";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// Đặt font chữ UTF-8 để không lỗi tiếng Việt
$conn->set_charset("utf8mb4");
// thoi gian thuc
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>