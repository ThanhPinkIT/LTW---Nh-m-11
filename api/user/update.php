<?php

require_once "../middleware/admin.php";

$data = json_decode(file_get_contents("php://input"),true);

$id = intval($data['id']);

$fullname = trim($data['fullname']);

$email = trim($data['email']);

$role = trim($data['role']);

$stmt = $conn->prepare("
UPDATE users
SET
fullname=?,
email=?,
role=?
WHERE id=?
");

$stmt->bind_param(
    "sssi",
    $fullname,
    $email,
    $role,
    $id
);

if($stmt->execute()){

    response(
        true,
        "User updated"
    );

}

response(false,"Update failed");