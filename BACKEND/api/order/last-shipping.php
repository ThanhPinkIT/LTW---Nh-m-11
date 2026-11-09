<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/order.php";

header("Cache-Control: no-store, no-cache, must-revalidate, private");

/*
Gợi ý điền sẵn form thanh toán: lấy thông tin giao hàng của đơn gần nhất
của chính người dùng (nếu chưa có đơn nào thì dùng họ tên trong tài khoản).
*/

ensure_order_shipping_columns($conn);

$user_id=(int)$_SESSION['user_id'];

$stmt=$conn->prepare("
SELECT receiver_name,receiver_phone,shipping_city,shipping_ward,shipping_address
FROM orders
WHERE user_id=? AND receiver_phone<>''
ORDER BY id DESC
LIMIT 1
");
$stmt->bind_param("i",$user_id);
$stmt->execute();

$last=$stmt->get_result()->fetch_assoc();

if(!$last){

    $stmt=$conn->prepare("SELECT fullname FROM users WHERE id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $u=$stmt->get_result()->fetch_assoc();

    $last=[
        "receiver_name"=>$u['fullname'] ?? '',
        "receiver_phone"=>'',
        "shipping_city"=>'',
        "shipping_ward"=>'',
        "shipping_address"=>''
    ];
}

success($last);
