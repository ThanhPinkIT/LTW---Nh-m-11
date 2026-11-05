<?php

session_start();

require_once "../config/response.php";

unset($_SESSION['cart']);
unset($_SESSION['voucher']);

success([],"Cart cleared");
