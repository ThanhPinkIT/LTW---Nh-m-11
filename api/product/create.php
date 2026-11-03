<?php

require_once "../config/database.php";
require_once "../config/response.php";

if($_SERVER['REQUEST_METHOD']!="POST"){
    error("POST only");
}

$name=$_POST['name'] ?? "";
$price=$_POST['price'] ?? 0;
$description=$_POST['description'] ?? "";
$category=$_POST['category_id'] ?? 0;

$image="";

if(isset($_FILES['image'])){

    $image=time()."_".$_FILES['image']['name'];

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "../../uploads/".$image
    );
}

$stmt=$conn->prepare("
INSERT INTO products
(name,price,description,image,category_id)
VALUES(?,?,?,?,?)
");

$stmt->bind_param(
"sdssi",
$name,
$price,
$description,
$image,
$category
);

if($stmt->execute()){

    success([
        "id"=>$conn->insert_id
    ],"Product created");

}

error("Create failed");