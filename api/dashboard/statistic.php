<?php

require_once "../middleware/admin.php";

$product = $conn->query("
SELECT COUNT(*) total
FROM products
")->fetch_assoc()['total'];

$category = $conn->query("
SELECT COUNT(*) total
FROM categories
")->fetch_assoc()['total'];

$user = $conn->query("
SELECT COUNT(*) total
FROM users
WHERE role='customer'
")->fetch_assoc()['total'];

$order = $conn->query("
SELECT COUNT(*) total
FROM orders
")->fetch_assoc()['total'];

$pending = $conn->query("
SELECT COUNT(*) total
FROM orders
WHERE status='Pending'
")->fetch_assoc()['total'];

$shipping = $conn->query("
SELECT COUNT(*) total
FROM orders
WHERE status='Shipping'
")->fetch_assoc()['total'];

$completed = $conn->query("
SELECT COUNT(*) total
FROM orders
WHERE status='Completed'
")->fetch_assoc()['total'];

$canceled = $conn->query("
SELECT COUNT(*) total
FROM orders
WHERE status='Cancelled'
")->fetch_assoc()['total'];

$revenue = $conn->query("
SELECT SUM(total_money) total
FROM orders
WHERE status='Completed'
")->fetch_assoc()['total'];

response(
    true,
    "Dashboard statistic",
    [
        "products"=>$product,
        "categories"=>$category,
        "customers"=>$user,
        "orders"=>$order,
        "pending"=>$pending,
        "shipping"=>$shipping,
        "completed"=>$completed,
        "canceled"=>$canceled,
        "revenue"=>$revenue ?? 0
    ]
);