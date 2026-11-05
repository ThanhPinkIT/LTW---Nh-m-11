<?php
// GET /voucher/index.php  -> danh sách voucher đang dùng được

session_start();

require_once "../config/database.php";
require_once "../config/response.php";

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

$stmt = $conn->prepare("
    SELECT
        v.code, v.description, v.type, v.value, v.max_discount, v.min_order,
        v.start_date, v.end_date, v.per_user_limit,
        IF(v.usage_limit IS NULL, NULL, v.usage_limit - v.used_count) AS remaining,
        (SELECT COUNT(*) FROM voucher_usages vu
          WHERE vu.voucher_id = v.id AND vu.user_id = ?) AS used_by_me
    FROM vouchers v
    WHERE v.is_active = 1
      AND (v.start_date IS NULL OR v.start_date <= NOW())
      AND (v.end_date   IS NULL OR v.end_date   >= NOW())
      AND (v.usage_limit IS NULL OR v.used_count < v.usage_limit)
    ORDER BY v.id ASC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $perUser = (int)$row['per_user_limit'];
    $usedByMe = (int)$row['used_by_me'];

    $data[] = [
        "code"         => $row['code'],
        "description"  => $row['description'],
        "type"         => $row['type'],
        "value"        => (float)$row['value'],
        "max_discount" => $row['max_discount'] !== null ? (float)$row['max_discount'] : null,
        "min_order"    => (float)$row['min_order'],
        "start_date"   => $row['start_date'],
        "end_date"     => $row['end_date'],
        "remaining"    => $row['remaining'] !== null ? (int)$row['remaining'] : null,
        // false nếu người dùng đã dùng hết số lần cho phép
        "available"    => ($perUser === 0 || $usedByMe < $perUser)
    ];
}

success($data, "Load vouchers successfully");
