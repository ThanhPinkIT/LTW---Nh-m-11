<?php

require_once "auth.php";

if($_SESSION['role']!="admin"){

    response(
        false,
        "Permission denied",
        null,
        403
    );

}