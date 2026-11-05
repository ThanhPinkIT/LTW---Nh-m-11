<?php
// POST /voucher/remove.php  -> gỡ voucher đang áp dụng

session_start();

require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/voucher.php";

unset($_SESSION['voucher']);

success(build_cart_totals($conn), "Đã gỡ mã giảm giá");
