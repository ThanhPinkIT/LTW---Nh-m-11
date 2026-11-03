<?php

require_once "../config/database.php";
require_once "../config/response.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    error("POST only");
}

$name = trim($_POST['name']);

if ($name == "") {
    error("Category name is required");
}

$stmt = $conn->prepare("INSERT INTO categories(name) VALUES(?)");
$stmt->bind_param("s", $name);

if ($stmt->execute()) {

    success([
        "id"=>$conn->insert_id
    ],"Category created");

}

error("Create failed");