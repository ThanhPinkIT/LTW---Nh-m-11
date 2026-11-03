<?php
require_once "../config/database.php";
require_once "../config/response.php";

// Xây dựng base URL động để tránh hardcode sai đường dẫn
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$base   = rtrim(str_replace('\\', '/', dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])))), '/');
$uploadUrl = $scheme . '://' . $host . $base . '/uploads/';

$sql="SELECT
id,
name,
price,
description,
image
FROM products
ORDER BY id DESC";

$result=$conn->query($sql);

$data=[];

while($row=$result->fetch_assoc())
{
    $row['image'] = $uploadUrl . $row['image'];

    $data[]=$row;
}

success($data,"Load products successfully");