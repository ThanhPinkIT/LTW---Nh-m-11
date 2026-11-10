<?php
$pageTitle = 'Chi tiết đơn hàng';
include 'includes/header.php';
?>
<div class="container my-4 order-detail-page">
    <div id="orderContent">
        <div class="text-center mt-5">
            <div class="spinner-border text-danger" role="status"></div>
        </div>
    </div>
</div>

<script src="assets/js/vendor/qrcode.js"></script>
<script src="assets/js/payment-qr.js?v=1"></script>
<script>
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const orderId = new URLSearchParams(location.search).get('id');

const STATUS = {
    Pending:    ['Chờ xử lý', 'history-badge-pending'],
    Processing: ['Đang xử lý', 'history-badge-processing'],
    Shipping:   ['Đang giao',  'history-badge-processing'],
    Completed:  ['Hoàn thành', 'history-badge-completed'],
    Cancelled:  ['Đã hủy',     'history-badge-cancelled']
};

function statusBadge(status) {
    const [label, cls] = STATUS[status] || [status, 'history-badge-default'];
    return `<span class="badge history-badge ${cls}">${esc(label)}</span>`;
}

function imgName(path) {
    const parts = String(path || '').split('/');
    return parts[parts.length - 1];
}

async function loadOrder() {
    const box = document.getElementById('orderContent');

    if (!orderId || !/^\d+$/.test(orderId)) {
        box.innerHTML = `<div class="alert alert-warning text-center mt-4">Mã đơn hàng không hợp lệ. <a href="history.php" class="alert-link">Về lịch sử mua hàng</a></div>`;
        return;
    }

    try {
        const res = await fetchAPI('/order/detail.php?id=' + encodeURIComponent(orderId));
        const o = res.data;
        const date = new Date(o.created_at).toLocaleString('vi-VN');
        const shipFee = Number(o.shipping_fee) || 0;
        const discount = Number(o.discount_amount) || 0;

        const rows = (o.items || []).map(it => `
            <tr>
                <td style="width:84px">
                    <img src="uploads/${encodeURIComponent(imgName(it.image))}" width="64" class="img-thumbnail"
                         onerror="this.onerror=null;this.src='https://placehold.co/200x200/eeeeee/999999?text=No+Image';" alt="">
                </td>
                <td class="text-start">${esc(it.name)}</td>
                <td class="text-end">${formatVND(it.price)}</td>
                <td class="text-center">${esc(it.quantity)}</td>
                <td class="text-end text-danger fw-bold">${formatVND(it.line_total)}</td>
            </tr>`).join('');

        box.innerHTML = `
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0">🧾 Đơn hàng #${esc(o.id)}</h3>
                <div>${statusBadge(o.status)}</div>
            </div>
            <div class="text-muted mb-3">Đặt lúc ${esc(date)}</div>

            <div class="row g-3">
                <div class="col-lg-5 order-lg-2">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-danger text-white"><h5 class="mb-0">📍 Thông tin nhận hàng</h5></div>
                        <div class="card-body">
                            <dl class="order-info mb-0">
                                <dt>Người nhận</dt><dd>${esc(o.receiver_name) || '—'}</dd>
                                <dt>Số điện thoại</dt><dd>${esc(o.receiver_phone) || '—'}</dd>
                                <dt>Địa chỉ</dt><dd>${esc(o.full_address) || '—'}</dd>
                                ${o.note ? `<dt>Ghi chú</dt><dd style="white-space:pre-line">${esc(o.note)}</dd>` : ''}
                                <dt>Thanh toán</dt><dd>${esc(o.payment_method)}</dd>
                            </dl>
                        </div>
                    </div>
                    ${o.status === 'Pending' && o.payment_method !== 'COD' ? `
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white"><h5 class="mb-0">📱 Thanh toán bằng QR</h5></div>
                        <div class="card-body" id="orderQR"></div>
                    </div>` : ''}
                </div>

                <div class="col-lg-7 order-lg-1">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-dark text-white"><h5 class="mb-0">🛍️ Sản phẩm đã đặt</h5></div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 text-center">
                                <thead class="table-light">
                                    <tr><th></th><th class="text-start">Sản phẩm</th><th class="text-end">Đơn giá</th><th>SL</th><th class="text-end">Thành tiền</th></tr>
                                </thead>
                                <tbody>${rows}</tbody>
                                <tfoot>
                                    <tr><td colspan="4" class="text-end">Tạm tính:</td><td class="text-end">${formatVND(o.subtotal)}</td></tr>
                                    <tr><td colspan="4" class="text-end">Phí vận chuyển:</td><td class="text-end">${formatVND(shipFee)}</td></tr>
                                    ${discount > 0 ? `<tr class="text-success"><td colspan="4" class="text-end">Giảm giá${o.voucher_code ? ' (' + esc(o.voucher_code) + ')' : ''}:</td><td class="text-end">-${formatVND(discount)}</td></tr>` : ''}
                                    <tr class="table-warning"><th colspan="4" class="text-end fs-5">Tổng thanh toán:</th><th class="text-end text-danger fs-5">${formatVND(o.total_money)}</th></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="history.php" class="btn history-home-btn">← Lịch sử mua hàng</a>
                ${o.status === 'Pending' ? `<button type="button" id="cancelBtn" class="btn btn-outline-danger">Hủy đơn hàng</button>` : ''}
            </div>
        `;

        const qrBox = document.getElementById('orderQR');
        if (qrBox) hienQRThanhToan(qrBox, o.total_money, PAYMENT_CONFIG.memoPrefix + ' DH' + o.id, 'Order');
        document.getElementById('cancelBtn')?.addEventListener('click', cancelOrder);
    } catch (err) {
        if (/unauthorized|login|đăng nhập/i.test(err.message)) {
            window.location.href = 'login.php';
            return;
        }
        const notFound = /not found/i.test(err.message);
        box.innerHTML = `
            <div class="alert alert-warning text-center mt-4">
                ${notFound ? 'Không tìm thấy đơn hàng này (hoặc đơn không thuộc tài khoản của bạn).' : 'Lỗi tải đơn hàng: ' + esc(err.message)}
                <a href="history.php" class="alert-link">Về lịch sử mua hàng</a>
            </div>`;
    }
}

async function cancelOrder() {
    if (!confirm('Bạn chắc chắn muốn hủy đơn hàng này?')) return;
    try {
        await fetchAPI('/order/cancel.php', { method: 'POST', body: { order_id: orderId } });
        if (typeof thongBao === 'function') thongBao('Đã hủy đơn hàng');
        loadOrder();
    } catch (err) {
        alert(err.message === 'Order not found or cannot be canceled'
            ? 'Đơn hàng này không thể hủy nữa (đã được xử lý).' : err.message);
    }
}

document.addEventListener('DOMContentLoaded', loadOrder);
</script>

<?php include 'includes/footer.php'; ?>
