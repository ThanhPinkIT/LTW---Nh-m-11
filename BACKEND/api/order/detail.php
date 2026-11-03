<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";

$id=intval($_GET['id']);

$stmt=$conn->prepare("
SELECT *
FROM orders
WHERE id=?
AND user_id=?
");

$stmt->bind_param(
"ii",
$id,
$_SESSION['user_id']
);

$stmt->execute();

$order=$stmt->get_result();

if($order->num_rows==0){

    error("Order not found");

}

$data=$order->fetch_assoc();

/*
Chi tiết sản phẩm
*/

$stmt=$conn->prepare("
SELECT
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

while($row=$result->fetch_assoc()){

    $items[]=$row;

}

$data['items']=$items;

success($data);