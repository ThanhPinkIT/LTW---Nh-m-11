<?php

require_once "../middleware/admin.php";

$data=json_decode(file_get_contents("php://input"),true);

$id=intval($data['id']);

$stmt=$conn->prepare("
DELETE FROM users
WHERE id=?
");

$stmt->bind_param("i",$id);

if($stmt->execute()){

    response(
        true,
        "Deleted successfully"
    );

}

response(false,"Delete failed");