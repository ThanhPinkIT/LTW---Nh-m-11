<?php
/*
 * Helper dùng chung cho tính năng giỏ hàng + voucher.
 * Yêu cầu: session đã được start và $conn (mysqli) đã được tạo.
 */

// Phí vận chuyển cố định cho mỗi đơn. Đặt = 0 nếu không muốn tính phí ship.
if (!defined('SHIPPING_FEE')) {
    define('SHIPPING_FEE', 30000);
}

// Lỗi nghiệp vụ của voucher (mã sai, hết hạn, hết lượt...) - message hiển thị được cho người dùng
class VoucherException extends RuntimeException {}

/**
 * Đọc giỏ hàng trong session, lấy giá THẬT từ database.
 * Trả về ['items' => [...], 'subtotal' => float]
 */
function get_cart_items(mysqli $conn): array
{
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        return ['items' => [], 'subtotal' => 0];
    }

    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $subtotal = 0;

    while ($row = $result->fetch_assoc()) {
        $qty = (int)$cart[$row['id']];
        $line = $row['price'] * $qty;
        $subtotal += $line;

        $items[] = [
            "id"       => $row['id'],
            "name"     => $row['name'],
            "price"    => $row['price'],
            "image"    => $row['image'],
            "quantity" => $qty,
            "subtotal" => $line
        ];
    }

    return ['items' => $items, 'subtotal' => $subtotal];
}

/**
 * Tìm voucher theo mã. $forUpdate = true để khóa dòng khi đang trong transaction.
 */
function find_voucher(mysqli $conn, string $code, bool $forUpdate = false): ?array
{
    $code = strtoupper(trim($code));

    $sql = "
        SELECT *,
            (start_date IS NOT NULL AND start_date > NOW()) AS not_started,
            (end_date IS NOT NULL AND end_date < NOW())     AS expired
        FROM vouchers
        WHERE code = ?
    ";
    if ($forUpdate) {
        $sql .= " FOR UPDATE";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?: null;
}

/**
 * Kiểm tra voucher có dùng được cho đơn hàng này không và tính số tiền giảm.
 * Ném VoucherException nếu không hợp lệ.
 */
function voucher_check(mysqli $conn, array $v, float $subtotal, float $shipping, ?int $userId): float
{
    if (!(int)$v['is_active']) {
        throw new VoucherException("Mã giảm giá đã bị vô hiệu hóa");
    }
    if ((int)$v['not_started']) {
        throw new VoucherException("Mã giảm giá chưa đến thời gian áp dụng");
    }
    if ((int)$v['expired']) {
        throw new VoucherException("Mã giảm giá đã hết hạn");
    }
    if ($v['usage_limit'] !== null && (int)$v['used_count'] >= (int)$v['usage_limit']) {
        throw new VoucherException("Mã giảm giá đã hết lượt sử dụng");
    }
    if ($subtotal < (float)$v['min_order']) {
        throw new VoucherException(
            "Đơn hàng tối thiểu " . number_format((float)$v['min_order'], 0, ',', '.') . "đ để dùng mã này"
        );
    }

    // Giới hạn số lần mỗi người dùng (chỉ kiểm tra được khi đã đăng nhập;
    // lúc checkout luôn đã đăng nhập nên vẫn được chặn)
    if ($userId !== null && (int)$v['per_user_limit'] > 0) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM voucher_usages WHERE voucher_id=? AND user_id=?");
        $vid = (int)$v['id'];
        $stmt->bind_param("ii", $vid, $userId);
        $stmt->execute();
        $used = (int)$stmt->get_result()->fetch_assoc()['c'];

        if ($used >= (int)$v['per_user_limit']) {
            throw new VoucherException("Bạn đã sử dụng mã này rồi");
        }
    }

    $max = $v['max_discount'] !== null ? (float)$v['max_discount'] : null;

    switch ($v['type']) {
        case 'percent':
            $discount = floor($subtotal * (float)$v['value'] / 100);
            if ($max !== null) $discount = min($discount, $max);
            break;

        case 'fixed':
            $discount = (float)$v['value'];
            if ($max !== null) $discount = min($discount, $max);
            break;

        case 'freeship':
            $discount = $shipping;
            if ($max !== null) $discount = min($discount, $max);
            break;

        default:
            throw new VoucherException("Loại voucher không hợp lệ");
    }

    // Không bao giờ giảm quá tổng tiền đơn hàng
    $discount = min($discount, $subtotal + $shipping);

    if ($discount <= 0) {
        throw new VoucherException("Mã giảm giá không áp dụng được cho đơn hàng này");
    }

    return $discount;
}

/**
 * Tính toàn bộ số liệu giỏ hàng (có voucher nếu đang áp dụng trong session).
 * 'total' giữ nguyên nghĩa cũ (= tạm tính) để không làm hỏng frontend hiện tại.
 */
function build_cart_totals(mysqli $conn): array
{
    $cart = get_cart_items($conn);
    $subtotal = $cart['subtotal'];
    $shipping = empty($cart['items']) ? 0 : SHIPPING_FEE;

    $discount = 0;
    $voucherInfo = null;
    $voucherMessage = null;

    if (!empty($_SESSION['voucher'])) {
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $v = $subtotal > 0 ? find_voucher($conn, $_SESSION['voucher']) : null;

        try {
            if (!$v) {
                throw new VoucherException("Mã giảm giá không còn khả dụng");
            }
            $discount = voucher_check($conn, $v, (float)$subtotal, (float)$shipping, $userId);
            $voucherInfo = [
                "code"        => $v['code'],
                "description" => $v['description'],
                "type"        => $v['type'],
                "discount"    => $discount
            ];
        } catch (VoucherException $e) {
            // Giỏ hàng thay đổi làm voucher không còn hợp lệ -> tự gỡ và báo cho FE
            unset($_SESSION['voucher']);
            $voucherMessage = $e->getMessage();
            $discount = 0;
        }
    }

    return [
        "items"           => $cart['items'],
        "total"           => $subtotal,                       // tạm tính (tương thích cũ)
        "subtotal"        => $subtotal,
        "shipping_fee"    => $shipping,
        "discount"        => $discount,
        "final_total"     => max(0, $subtotal + $shipping - $discount),
        "voucher"         => $voucherInfo,
        "voucher_message" => $voucherMessage
    ];
}

/**
 * Hoàn lại lượt dùng voucher của một đơn hàng (khi đơn bị hủy).
 * An toàn khi gọi nhiều lần: chỉ hoàn một lần.
 */
function voucher_release_usage(mysqli $conn, int $orderId): void
{
    $stmt = $conn->prepare("SELECT voucher_id FROM voucher_usages WHERE order_id=?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        return;
    }

    $voucherId = (int)$row['voucher_id'];

    $stmt = $conn->prepare("DELETE FROM voucher_usages WHERE order_id=?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE vouchers SET used_count = GREATEST(used_count - 1, 0) WHERE id=?");
    $stmt->bind_param("i", $voucherId);
    $stmt->execute();
}
