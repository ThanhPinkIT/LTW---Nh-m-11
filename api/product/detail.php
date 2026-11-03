<?php

require_once "../config/database.php";
require_once "../config/response.php";

// Xây dựng base URL động để tránh hardcode sai đường dẫn
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$base   = rtrim(str_replace('\\', '/', dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])))), '/');
$uploadUrl = $scheme . '://' . $host . $base . '/uploads/';

if (!isset($_GET['id'])) {
    error("Missing product id");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){
    error("Product not found",404);
}

$product=$result->fetch_assoc();

$product['image'] = $uploadUrl . $product['image'];

success($product);