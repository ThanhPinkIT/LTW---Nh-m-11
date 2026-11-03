<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";

$user=$_SESSION['user_id'];

$stmt=$conn->prepare("
SELECT *
FROM orders
WHERE user_id=?
ORDER BY id DESC
");

$stmt->bind_param("i",$user);

$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row=$result->fetch_assoc()){

    $data[]=$row;

}

success($data);