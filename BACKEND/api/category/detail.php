<?php

require_once "../config/database.php";
require_once "../config/response.php";

if (!isset($_GET['id'])) {
    error("Missing category id");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    error("Category not found",404);
}

success($result->fetch_assoc());