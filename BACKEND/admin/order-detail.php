<?php
include 'header.php';
require_once "../api/helper/voucher.php";
require_once "../api/helper/order.php";
ensure_order_shipping_columns($conn);

$id = intval($_GET['id'] ?? 0);
$message = "";

// Cập nhật trạng thái ngay tại trang chi tiết
if (isset($_POST['update_status'])) {
    $status = $_POST['status'] ?? '';
    $valid = ['Pending', 'Processing', 'Shipping', 'Completed', 'Cancelled'];
    if (in_array($status, $valid, true)) {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            if ($status === 'Cancelled') {
                voucher_release_usage($conn, $id);
            }
            $message = "<div class='alert alert-success'>Cập nhật trạng thái thành công.</div>";
        }
    }
}

$stmt = $conn->prepare("
    SELECT o.*, u.username, u.fullname, u.email
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE o.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "<div class='alert alert-warning'>Không tìm thấy đơn hàng. <a href='order.php'>Quay lại danh sách</a></div>";
    include 'footer.php';
    exit();
}

$stmt = $conn->prepare("
    SELECT od.quantity, od.price, p.name, p.image
    FROM order_details od
    JOIN products p ON p.id = od.product_id
    WHERE od.order_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$subtotal = 0;
foreach ($items as $it) {
    $subtotal += $it['price'] * $it['quantity'];
}

$statusLabels = [
    'Pending'    => 'Chờ xử lý',
    'Processing' => 'Đang xử lý',
    'Shipping'   => 'Đang giao',
    'Completed'  => 'Hoàn thành',
    'Cancelled'  => 'Đã hủy',
];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h2 class="mb-0">🧾 Đơn hàng #<?= (int)$order['id'] ?></h2>
    <a href="order.php" class="btn btn-outline-secondary">← Danh sách đơn hàng</a>
</div>

<?= $message ?>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card shadow h-100">
            <div class="card-header bg-dark text-white">📍 Thông tin nhận hàng</div>
            <div class="card-body">
                <?php if (trim($order['receiver_name']) === ''): ?>
                    <div class="alert alert-secondary py-2">Đơn cũ, chưa có thông tin giao hàng.</div>
                <?php endif; ?>
                <table class="table table-borderless mb-0">
                    <tr><th style="width:130px">Người nhận</th><td><?= htmlspecialchars($order['receiver_name'] ?: '—') ?></td></tr>
                    <tr><th>Số điện thoại</th><td>
                        <?php if ($order['receiver_phone'] !== ''): ?>
                            <a href="tel:<?= htmlspecialchars($order['receiver_phone']) ?>"><?= htmlspecialchars($order['receiver_phone']) ?></a>
                        <?php else: ?>—<?php endif; ?>
                    </td></tr>
                    <tr><th>Địa chỉ</th><td><?= htmlspecialchars(format_order_address($order) ?: '—') ?></td></tr>
                    <?php if (trim((string)$order['note']) !== ''): ?>
                    <tr><th>Ghi chú</th><td style="white-space:pre-line"><?= htmlspecialchars($order['note']) ?></td></tr>
                    <?php endif; ?>
                    <tr><th>Thanh toán</th><td><?= htmlspecialchars($order['payment_method']) ?></td></tr>
                    <tr><th>Ngày đặt</th><td><?= date("d/m/Y H:i", strtotime($order['created_at'])) ?></td></tr>
                    <tr><th>Tài khoản đặt</th><td>
                        <?= htmlspecialchars($order['fullname']) ?>
                        <small class="text-muted">(<?= htmlspecialchars($order['username']) ?> · <?= htmlspecialchars($order['email']) ?>)</small>
                    </td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow h-100">
            <div class="card-header bg-dark text-white">🛍️ Sản phẩm</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th></th><th>Sản phẩm</th><th class="text-end">Đơn giá</th><th class="text-center">SL</th><th class="text-end">Thành tiền</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td style="width:70px">
                                <img src="../uploads/<?= rawurlencode(basename($it["image"])) ?>" width="56" class="img-thumbnail" alt="">
                            </td>
                            <td><?= htmlspecialchars($it['name']) ?></td>
                            <td class="text-end"><?= number_format($it['price']) ?> đ</td>
                            <td class="text-center"><?= (int)$it['quantity'] ?></td>
                            <td class="text-end"><?= number_format($it['price'] * $it['quantity']) ?> đ</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="4" class="text-end">Tạm tính:</td><td class="text-end"><?= number_format($subtotal) ?> đ</td></tr>
                        <tr><td colspan="4" class="text-end">Phí vận chuyển:</td><td class="text-end"><?= number_format($order['shipping_fee']) ?> đ</td></tr>
                        <?php if ($order['discount_amount'] > 0): ?>
                        <tr class="text-success"><td colspan="4" class="text-end">Giảm giá<?= $order['voucher_code'] ? ' (' . htmlspecialchars($order['voucher_code']) . ')' : '' ?>:</td><td class="text-end">-<?= number_format($order['discount_amount']) ?> đ</td></tr>
                        <?php endif; ?>
                        <tr class="table-warning"><th colspan="4" class="text-end fs-5">Tổng thanh toán:</th><th class="text-end text-danger fs-5"><?= number_format($order['total_money']) ?> đ</th></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mt-3">
    <div class="card-body">
        <form method="POST" class="d-flex flex-wrap align-items-center gap-2">
            <strong>Trạng thái:</strong>
            <select name="status" class="form-select" style="max-width:220px">
                <?php foreach ($statusLabels as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $order['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <button name="update_status" class="btn btn-success">Lưu trạng thái</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
