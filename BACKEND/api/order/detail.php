<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/order.php";

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");

ensure_order_shipping_columns($conn);

$id=intval($_GET['id'] ?? 0);
$user_id=(int)$_SESSION['user_id'];
$is_admin=(($_SESSION['role'] ?? '')==='admin');

if($id<=0){
    error("Order not found",404);
}

/*
Khách hàng: chỉ xem được đơn CỦA MÌNH (user_id khớp tài khoản đang đăng nhập).
Admin: xem được mọi đơn.
Đơn của người khác -> trả về "Order not found" (không tiết lộ đơn có tồn tại hay không).
*/
if($is_admin){

    $stmt=$conn->prepare("
    SELECT o.*, u.username, u.email
    FROM orders o
    JOIN users u ON u.id=o.user_id
    WHERE o.id=?
    ");
    $stmt->bind_param("i",$id);

}else{

    $stmt=$conn->prepare("
    SELECT o.*, u.username, u.email
    FROM orders o
    JOIN users u ON u.id=o.user_id
    WHERE o.id=?
    AND o.user_id=?
    ");
    $stmt->bind_param("ii",$id,$user_id);

}

$stmt->execute();

$order=$stmt->get_result();

if($order->num_rows==0){

    error("Order not found",404);

}

$data=$order->fetch_assoc();

$data['full_address']=format_order_address($data);

/*
Chi tiết sản phẩm
*/

$stmt=$conn->prepare("
SELECT
od.product_id,
od.quantity,
od.price,
p.name,
p.image
FROM order_details od
JOIN products p
ON od.product_id=p.id
WHERE od.order_id=?
");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

$items=[];
$subtotal=0;

while($row=$result->fetch_assoc()){

    $row['line_total']=(float)$row['price']*(int)$row['quantity'];
    $subtotal+=$row['line_total'];
    $items[]=$row;

}

$data['items']=$items;
$data['subtotal']=$subtotal;

success($data);
