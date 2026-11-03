<?php

function response($success, $message = "", $data = null, $code = 200)
{
    http_response_code($code);

    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

    exit;
}