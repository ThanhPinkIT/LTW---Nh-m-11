<?php

session_start();

require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/voucher.php";

if(!isset($_SESSION['cart'])){
    $_SESSION['cart']=[];
}

// Trả về: items, total (= tạm tính), subtotal, shipping_fee, discount,
// final_total, voucher, voucher_message
success(build_cart_totals($conn));
