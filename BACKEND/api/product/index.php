<?php
require_once "../config/database.php";
require_once "../config/response.php";

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

// Xây dựng base URL động để tránh hardcode sai đường dẫn
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$base   = rtrim(str_replace('\\', '/', dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])))), '/');
$uploadUrl = $scheme . '://' . $host . $base . '/uploads/';

try {
    $result=$conn->query("SELECT
    id, category_id, name, price, original_price,
    display_rating, display_review_count, description, image
    FROM products
    ORDER BY id DESC");
} catch (Throwable $e) {
    $result=$conn->query("SELECT
    id, category_id, name, price, description, image
    FROM products
    ORDER BY id DESC");
}

$data=[];

while($row=$result->fetch_assoc())
{
    $row['image'] = $uploadUrl . $row['image'];

    $data[]=$row;
}

success($data,"Load products successfully");