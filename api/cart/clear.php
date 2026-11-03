<?php

session_start();

require_once "../config/response.php";

unset($_SESSION['cart']);

success([],"Cart cleared");