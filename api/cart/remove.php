<?php

session_start();

require_once "../config/response.php";

$id=intval($_POST['product_id']);

if(isset($_SESSION['cart'][$id])){

    unset($_SESSION['cart'][$id]);

}

success($_SESSION['cart'],"Removed");