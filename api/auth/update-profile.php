<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";

$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');

if ($fullname === '' || $email === '') {
    error("Họ tên và email không được để trống");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error("Email không hợp lệ");
}

$id = $_SESSION['user_id'];

$stmt=$conn->prepare("
UPDATE users
SET
fullname=?,
email=?
WHERE id=?
");

$stmt->bind_param(
"ssi",
$fullname,
$email,
$id
);

if($stmt->execute()){

    success([],"Profile updated");

}

error("Update failed");