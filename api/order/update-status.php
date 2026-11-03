<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";

if($_SESSION['role']!="admin"){
    error("Permission denied",403);
}

$id=$_POST['order_id'];

$status=$_POST['status'];

$stmt=$conn->prepare("
UPDATE orders
SET status=?
WHERE id=?
");

$stmt->bind_param(
"si",
$status,
$id
);

if($stmt->execute()){

    success([],"Status updated");

}

error("Update failed");