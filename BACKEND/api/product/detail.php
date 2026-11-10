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

// thông số kỹ thuật (nếu chưa tạo bảng product_specs thì bỏ qua, không làm hỏng trang)
$product['specs'] = [];
try {
    $stmt2 = $conn->prepare("
        SELECT spec_name AS name, spec_value AS value
        FROM product_specs
        WHERE product_id=?
        ORDER BY sort_order, id
    ");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $rs = $stmt2->get_result();
    while ($row = $rs->fetch_assoc()) {
        $product['specs'][] = $row;
    }
} catch (Throwable $e) {
}

// danh sách ảnh để lướt xem (ảnh chính đứng đầu, sau đó là ảnh phụ)
// nếu chưa tạo bảng product_images thì chỉ có ảnh chính
$product['images'] = [$product['image']];
try {
    $stmt3 = $conn->prepare("
        SELECT image
        FROM product_images
        WHERE product_id=?
        ORDER BY sort_order, id
    ");
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    $rs3 = $stmt3->get_result();
    while ($row = $rs3->fetch_assoc()) {
        $product['images'][] = $uploadUrl . $row['image'];
    }
} catch (Throwable $e) {
}

success($product);