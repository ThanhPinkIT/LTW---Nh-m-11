<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";

$id=intval($_POST['order_id']);

$stmt=$conn->prepare("
UPDATE orders
SET status='Cancelled'
WHERE id=?
AND user_id=?
AND status='Pending'
");

$stmt->bind_param(
"ii",
$id,
$_SESSION['user_id']
);

if($stmt->execute()){

    success([],"Order canceled");

}

error("Cancel failed");