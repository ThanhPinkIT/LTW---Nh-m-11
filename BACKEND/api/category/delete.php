<?php

require_once "../config/database.php";
require_once "../config/response.php";

if(!isset($_GET['id'])){
    error("Missing id");
}

$id=intval($_GET['id']);

/*
Kiểm tra còn sản phẩm thuộc danh mục không
*/

$stmt=$conn->prepare("
SELECT COUNT(*) total
FROM products
WHERE category_id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$total=$stmt->get_result()->fetch_assoc()['total'];

if($total>0){

    error("Category still contains products");

}

$stmt=$conn->prepare("
DELETE FROM categories
WHERE id=?
");

$stmt->bind_param("i",$id);

if($stmt->execute()){

    success([],"Deleted");

}

error("Delete failed");