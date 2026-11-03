<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";

if(empty($_SESSION['cart'])){
    error("Cart is empty");
}

$user_id=$_SESSION['user_id'];
$payment=$_POST['payment_method'] ?? "COD";

$total=0;

$ids=implode(",",array_keys($_SESSION['cart']));

$sql="SELECT * FROM products WHERE id IN ($ids)";
$result=$conn->query($sql);

$products=[];

while($row=$result->fetch_assoc()){

    $qty=$_SESSION['cart'][$row['id']];

    $subtotal=$row['price']*$qty;

    $total+=$subtotal;

    $products[]=[
        "product"=>$row,
        "qty"=>$qty
    ];
}

$stmt=$conn->prepare("
INSERT INTO orders
(user_id,total_money,payment_method,status)
VALUES(?,?,?,'Pending')
");

$stmt->bind_param(
"ids",
$user_id,
$total,
$payment
);

if(!$stmt->execute()){
    error("Create order failed");
}

$order_id=$conn->insert_id;

/*
Nếu có bảng order_details
*/

foreach($products as $item){

    $p=$item['product'];

    $stmt=$conn->prepare("
    INSERT INTO order_details
    (order_id,product_id,quantity,price)
    VALUES(?,?,?,?)
    ");

    $stmt->bind_param(
    "iiid",
    $order_id,
    $p['id'],
    $item['qty'],
    $p['price']
    );

    $stmt->execute();

}

unset($_SESSION['cart']);

success([
    "order_id"=>$order_id,
    "total"=>$total
],"Checkout success");