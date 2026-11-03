<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "web_ban_hang";
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

date_default_timezone_set('Asia/Ho_Chi_Minh');
?>

