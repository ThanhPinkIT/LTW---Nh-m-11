<?php
header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "success" => true,
    "application" => "Shopping API",
    "version" => "1.0.0",
    "status" => "Running",
    "developer" => "Lam Truong Dang",
    "server_time" => date("Y-m-d H:i:s"),
    "endpoints" => [
        "auth" => [
            "login",
            "logout",
            "register",
            "profile",
            "update-profile"
        ],
        "product" => [
            "index",
            "detail",
            "create",
            "update",
            "delete"
        ],
        "category" => [
            "index",
            "create",
            "update",
            "delete"
        ],
        "cart" => [
            "index",
            "add",
            "update",
            "remove",
            "clear"
        ],
        "order" => [
            "checkout",
            "history",
            "detail",
            "cancel",
            "update-status"
        ],
        "user" => [
            "index",
            "detail",
            "update",
            "delete",
            "change-password"
        ],
        "dashboard" => [
            "statistic"
        ]
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);