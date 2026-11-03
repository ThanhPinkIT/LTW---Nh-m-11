<?php

require_once "../config/database.php";
require_once "../config/response.php";

if($_SERVER['REQUEST_METHOD']!="POST"){
    error("POST only");
}

$id=$_POST['id'];

$name=$_POST['name'];
$price=$_POST['price'];
$description=$_POST['description'];
$category=$_POST['category_id'];

$stmt=$conn->prepare("
UPDATE products
SET
name=?,
price=?,
description=?,
category_id=?
WHERE id=?
");

$stmt->bind_param(
"sdsii",
$name,
$price,
$description,
$category,
$id
);

if($stmt->execute()){

    success([],"Updated");

}

error("Update failed");