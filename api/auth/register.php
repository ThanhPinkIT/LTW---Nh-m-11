<?php

require_once "../config/database.php";
require_once "../config/response.php";

if($_SERVER['REQUEST_METHOD']!="POST"){
    error("POST only");
}

$username=trim($_POST['username']);
$password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
$fullname=trim($_POST['fullname']);
$email=trim($_POST['email']);

$stmt=$conn->prepare("
SELECT id
FROM users
WHERE username=?
");

$stmt->bind_param("s",$username);
$stmt->execute();

if($stmt->get_result()->num_rows>0){
    error("Username already exists");
}

$role="customer";

$stmt=$conn->prepare("
INSERT INTO users
(username,password,fullname,email,role)
VALUES(?,?,?,?,?)
");

$stmt->bind_param(
"sssss",
$username,
$password,
$fullname,
$email,
$role
);

if($stmt->execute()){

    success([
        "id"=>$conn->insert_id
    ],"Register success");

}

error("Register failed");
