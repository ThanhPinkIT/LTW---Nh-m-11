<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/order.php";

// Dữ liệu riêng tư của từng người dùng -> không cho trình duyệt/proxy lưu cache
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");

ensure_order_shipping_columns($conn);

// CHỈ lấy đơn của chính người đang đăng nhập (kể cả admin: admin xem toàn bộ trong trang quản trị)
$user=(int)$_SESSION['user_id'];

$stmt=$conn->prepare("
SELECT *
FROM orders
WHERE user_id=?
ORDER BY id DESC
");

$stmt->bind_param("i",$user);

$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row=$result->fetch_assoc()){

    $row['full_address']=format_order_address($row);
    $data[]=$row;

}

success($data);
