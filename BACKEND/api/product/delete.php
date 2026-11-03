<?php

require_once "../config/database.php";
require_once "../config/response.php";

if(!isset($_GET['id'])){
    error("Missing id");
}

$id=intval($_GET['id']);

$stmt=$conn->prepare("
SELECT image
FROM products
WHERE id=?
");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){
    error("Product not found");
}

$product=$result->fetch_assoc();

$file="../../uploads/".$product['image'];

if(file_exists($file)){
    unlink($file);
}

$stmt=$conn->prepare("
DELETE FROM products
WHERE id=?
");

$stmt->bind_param("i",$id);

if($stmt->execute()){

    success([],"Deleted");

}

error("Delete failed");