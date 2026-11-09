<?php

// Cho phép CORS khi FE và BE gọi chéo nhau hoặc khác port
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$user = "root";
$password = "";
$database = "web_ban_hang";
$port = 3306;

// Trên hosting: file env.php (không đưa lên Git) sẽ ghi đè thông tin DB bên trên
$envFile = __DIR__ . "/../../config/env.php";
if (file_exists($envFile)) {
    require $envFile;
    $password = $pass;
    $database = $dbname;
}

try {
    $conn = new mysqli(
        $host,
        $user,
        $password,
        $database,
        $port
    );

    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);

    exit();
}

