<?php
/*
 * Helper cho đơn hàng: thông tin giao hàng (họ tên, SĐT, địa chỉ...)
 * - ensure_order_shipping_columns(): tự thêm cột còn thiếu vào bảng orders
 * - read_shipping_input(): kiểm tra & chuẩn hóa dữ liệu khách nhập (server-side)
 * - format_order_address(): ghép địa chỉ đầy đủ để hiển thị
 */

function ensure_order_shipping_columns(mysqli $conn): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $need = [
        'receiver_name'    => "VARCHAR(100) NOT NULL DEFAULT ''",
        'receiver_phone'   => "VARCHAR(20) NOT NULL DEFAULT ''",
        'shipping_city'    => "VARCHAR(100) NOT NULL DEFAULT ''",
        'shipping_ward'    => "VARCHAR(100) NOT NULL DEFAULT ''",
        'shipping_address' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'note'             => "VARCHAR(500) DEFAULT NULL",
    ];

    try {
        $have = [];
        $res = $conn->query("SHOW COLUMNS FROM orders");
        while ($r = $res->fetch_assoc()) {
            $have[$r['Field']] = true;
        }
        foreach ($need as $col => $def) {
            if (!isset($have[$col])) {
                $conn->query("ALTER TABLE orders ADD COLUMN `$col` $def");
            }
        }
    } catch (Throwable $e) {
        // Không có quyền ALTER -> bỏ qua, người dùng cần chạy order_shipping.sql
    }
}

function clean_text(string $s, int $max): string
{
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s);   // bỏ ký tự điều khiển
    $s = preg_replace('/[ \t]+/u', ' ', $s);
    return mb_substr(trim($s), 0, $max);
}

/**
 * @return array [data|null, errorMessage|null]
 */
function read_shipping_input(array $src): array
{
    $name  = clean_text((string)($src['receiver_name'] ?? ''), 100);
    $city  = clean_text((string)($src['shipping_city'] ?? ''), 100);
    $ward  = clean_text((string)($src['shipping_ward'] ?? ''), 100);
    $addr  = clean_text((string)($src['shipping_address'] ?? ''), 255);
    $note  = clean_text(str_replace(["\r\n", "\r"], "\n", (string)($src['note'] ?? '')), 500);

    // SĐT: bỏ khoảng trắng, dấu chấm, gạch; đổi +84 / 84 về đầu 0
    $phone = preg_replace('/[\s.\-()]/', '', (string)($src['receiver_phone'] ?? ''));
    if (strpos($phone, '+84') === 0) {
        $phone = '0' . substr($phone, 3);
    } elseif (strpos($phone, '84') === 0 && strlen($phone) === 11) {
        $phone = '0' . substr($phone, 2);
    }

    if (mb_strlen($name) < 2) {
        return [null, "Vui lòng nhập họ và tên người nhận"];
    }
    if (!preg_match('/^0\d{9}$/', $phone)) {
        return [null, "Số điện thoại không hợp lệ (gồm 10 chữ số, bắt đầu bằng 0)"];
    }
    if (mb_strlen($city) < 2) {
        return [null, "Vui lòng nhập Tỉnh / Thành phố"];
    }
    if (mb_strlen($ward) < 2) {
        return [null, "Vui lòng nhập Phường / Xã"];
    }
    if (mb_strlen($addr) < 5) {
        return [null, "Vui lòng nhập địa chỉ cụ thể (số nhà, tên đường...)"];
    }

    return [[
        'receiver_name'    => $name,
        'receiver_phone'   => $phone,
        'shipping_city'    => $city,
        'shipping_ward'    => $ward,
        'shipping_address' => $addr,
        'note'             => $note,
    ], null];
}

function format_order_address(array $o): string
{
    $parts = array_filter([
        $o['shipping_address'] ?? '',
        $o['shipping_ward'] ?? '',
        $o['shipping_city'] ?? '',
    ], fn($p) => trim((string)$p) !== '');
    return implode(', ', $parts);
}
