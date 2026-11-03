<?php
require_once "../config/database.php";
require_once "../config/response.php";

header("Content-Type: application/json; charset=UTF-8");

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId || $productId <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID sản phẩm không hợp lệ"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "
    SELECT
        r.rating,
        r.comment,
        r.created_at,
        u.fullname
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC, r.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

success($data, "Load reviews successfully");
