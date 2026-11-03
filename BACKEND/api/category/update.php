<?php

require_once "../config/database.php";
require_once "../config/response.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    error("POST only");
}

$id = intval($_POST['id']);
$name = trim($_POST['name']);

$stmt = $conn->prepare("
UPDATE categories
SET name=?
WHERE id=?
");

$stmt->bind_param("si",$name,$id);

if($stmt->execute()){

    success([],"Updated");

}

error("Update failed");