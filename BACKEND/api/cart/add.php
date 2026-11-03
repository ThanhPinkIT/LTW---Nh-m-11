<?php

session_start();

require_once "../config/database.php";
require_once "../config/response.php";

$id=intval($_POST['product_id']);
$qty=intval($_POST['quantity']);

if($qty<=0){
    $qty=1;
}

$stmt=$conn->prepare("SELECT id FROM products WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

if($stmt->get_result()->num_rows==0){
    error("Product not found");
}

if(!isset($_SESSION['cart'])){
    $_SESSION['cart']=[];
}

if(isset($_SESSION['cart'][$id])){

    $_SESSION['cart'][$id]+=$qty;

}else{

    $_SESSION['cart'][$id]=$qty;

}

success($_SESSION['cart'],"Added to cart");