<?php

require_once "../middleware/auth.php";

$data=json_decode(file_get_contents("php://input"),true);

$id=$_SESSION['user_id'];

$old=$data['old_password'];

$new=$data['new_password'];

$stmt=$conn->prepare("
SELECT password
FROM users
WHERE id=?
");

$stmt->bind_param("i",$id);

$stmt->execute();

$user=$stmt->get_result()->fetch_assoc();

if(!$user){

    response(false,"User not found");
    exit();

}

if(!password_verify($old,$user['password'])){

    response(false,"Old password incorrect");
    exit();

}

$newHash=password_hash(
    $new,
    PASSWORD_DEFAULT
);

$stmt=$conn->prepare("
UPDATE users
SET password=?
WHERE id=?
");

$stmt->bind_param(
    "si",
    $newHash,
    $id
);

$stmt->execute();

response(
    true,
    "Password changed successfully"
);