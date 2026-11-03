<?php

require_once "../config/database.php";
require_once "../config/response.php";

$sql = "SELECT * FROM categories ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

success($data);