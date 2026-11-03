<?php

session_start();

require_once "../config/response.php";

$id=intval($_POST['product_id']);
$qty=intval($_POST['quantity']);

if(!isset($_SESSION['cart'][$id])){
    error("Item not found");
}

if($qty<=0){

    unset($_SESSION['cart'][$id]);

}else{

    $_SESSION['cart'][$id]=$qty;

}

success($_SESSION['cart'],"Updated");