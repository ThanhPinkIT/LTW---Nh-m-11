<?php

session_start();

require_once "../config/database.php";
require_once "../config/response.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    error("POST only");
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username == "" || $password == "") {
    error("Username and password are required");
}

$stmt = $conn->prepare("
SELECT id,username,password,fullname,email,role
FROM users
WHERE username=?
");

$stmt->bind_param("s",$username);
$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){
    error("Username or password incorrect",401);
}

$user=$result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    error("Username or password incorrect", 401);
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

unset($user['password']);

success($user, "Login success");
