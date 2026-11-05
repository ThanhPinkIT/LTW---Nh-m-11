<?php
// POST /voucher/apply.php   body: code=...
// Kiểm tra mã theo giỏ hàng hiện tại, lưu vào session và trả về số liệu giỏ hàng mới.

session_start();

require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/voucher.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    error("POST only");
}

$code = strtoupper(trim($_POST['code'] ?? ''));

if ($code === '') {
    error("Vui lòng nhập mã giảm giá");
}

$cart = get_cart_items($conn);

if (empty($cart['items'])) {
    error("Giỏ hàng đang trống");
}

$voucher = find_voucher($conn, $code);

if (!$voucher) {
    error("Mã giảm giá không hợp lệ", 404);
}

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

try {
    voucher_check($conn, $voucher, (float)$cart['subtotal'], (float)SHIPPING_FEE, $userId);
} catch (VoucherException $e) {
    error($e->getMessage());
}

$_SESSION['voucher'] = $voucher['code'];

success(build_cart_totals($conn), "Áp dụng mã " . $voucher['code'] . " thành công");
