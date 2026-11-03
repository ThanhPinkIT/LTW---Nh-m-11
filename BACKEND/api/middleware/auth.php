<?php

session_start();

require_once "../../config/db.php";
require_once "../helper/response.php";

if(!isset($_SESSION['user_id'])){

    response(
        false,
        "Unauthorized",
        null,
        401
    );

}