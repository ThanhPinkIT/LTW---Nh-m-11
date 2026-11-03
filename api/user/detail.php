<?php

require_once "../middleware/admin.php";

if(!isset($_GET['id'])){
    response(false,"Missing user id");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
SELECT
id,
username,
fullname,
email,
role,
created_at
FROM users
WHERE id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if(!$user){
    response(false,"User not found",null,404);
}

response(
    true,
    "Success",
    $user
);