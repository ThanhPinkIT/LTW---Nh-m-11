<?php

session_start();

require_once "../config/response.php";

session_destroy();

success([],"Logout success");