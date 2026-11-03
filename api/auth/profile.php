<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";

$id=$_SESSION['user_id'];

$stmt=$conn->prepare("
SELECT
id,
username,
fullname,
email,
role
FROM users
WHERE id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$user=$stmt->get_result()->fetch_assoc();

success($user);