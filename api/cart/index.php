<?php

session_start();

require_once "../config/database.php";
require_once "../config/response.php";

if(!isset($_SESSION['cart'])){
    $_SESSION['cart']=[];
}

$data=[];
$total=0;

if(!empty($_SESSION['cart'])){

    $ids=implode(",",array_keys($_SESSION['cart']));

    $sql="SELECT * FROM products WHERE id IN ($ids)";

    $result=$conn->query($sql);

    while($row=$result->fetch_assoc()){

        $qty=$_SESSION['cart'][$row['id']];

        $subtotal=$row['price']*$qty;

        $total+=$subtotal;

        $data[]=[
            "id"=>$row['id'],
            "name"=>$row['name'],
            "price"=>$row['price'],
            "image"=>$row['image'],
            "quantity"=>$qty,
            "subtotal"=>$subtotal
        ];
    }

}

success([
    "items"=>$data,
    "total"=>$total
]);