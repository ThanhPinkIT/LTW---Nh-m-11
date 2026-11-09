<?php
$pageTitle = 'Lịch sử mua hàng';
include 'includes/header.php';
?>
<div class="container mt-5">
    <h2 class="mb-4 text-center"> 📦 Lịch sử mua hàng </h2>
    
    <div id="historyContent">
        <div class="col-12 text-center mt-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<script>
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function getStatusBadge(status) {
    switch (status) {
        case 'Pending':
            return '<span class="badge history-badge history-badge-pending">Chờ xử lý</span>';
        case 'Processing':
        case 'Shipping':
            return '<span class="badge history-badge history-badge-processing">Đang giao</span>';
        case 'Completed':
            return '<span class="badge history-badge history-badge-completed">Hoàn thành</span>';
        case 'Cancelled':
            return '<span class="badge history-badge history-badge-cancelled">Đã hủy</span>';
        default:
            return `<span class="badge history-badge history-badge-default">${esc(status)}</span>`;
    }
}

async function loadHistory() {
    const container = document.getElementById('historyContent');
    
    try {
        const res = await fetchAPI('/order/history.php');
        const orders = res.data;
        
        if (!orders || orders.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info text-center mt-4">
                    Bạn chưa có đơn hàng nào!
                    <a href="index.php" class="alert-link">Mua sắm ngay</a>
                </div>
            `;
            return;
        }
        
        let rows = '';
        orders.forEach((order, index) => {
            const date = new Date(order.created_at).toLocaleString('vi-VN');
            const detailUrl = `order-detail.php?id=${encodeURIComponent(order.id)}`;
            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td><a href="${detailUrl}" class="fw-bold text-primary text-decoration-none">#${esc(order.id)}</a></td>
                    <td>${date}</td>
                    <td class="text-start">
                        <div class="fw-semibold">${esc(order.receiver_name) || '<span class="text-muted">—</span>'}</div>
                        ${order.receiver_phone ? `<small class="text-muted">${esc(order.receiver_phone)}</small>` : ''}
                    </td>
                    <td class="text-danger fw-bold">
                        ${formatVND(order.total_money)}
                        ${Number(order.discount_amount) > 0 ? `<br><small class="text-success fw-normal">Voucher ${esc(order.voucher_code)}: -${formatVND(order.discount_amount)}</small>` : ''}
                    </td>
                    <td>${esc(order.payment_method)}</td>
                    <td>${getStatusBadge(order.status)}</td>
                    <td><a href="${detailUrl}" class="btn btn-sm btn-outline-danger">Xem</a></td>
                </tr>
            `;
        });
        
        container.innerHTML = `
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover table-bordered align-middle text-center bg-white mb-0">
                    <thead class="history-table-head">
                        <tr>
                            <th>STT</th>
                            <th>Mã Đơn Hàng</th>
                            <th>Ngày Đặt</th>
                            <th>Người Nhận</th>
                            <th>Tổng Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th>Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows}
                    </tbody>
                
                </table>
            </div>

            <div class="mt-3 text-start">
                <a href="index.php" class="btn history-home-btn">
                    ← Quay lại trang chủ
                </a>
            </div>
        `;

        
    } catch (err) {
        if (/unauthorized|login|đăng nhập/i.test(err.message)) {
            window.location.href = 'login.php';
        } else {
            container.innerHTML = `<div class="alert alert-danger">Lỗi tải lịch sử đơn hàng: ${esc(err.message)}</div>`;
        }
    }
}

document.addEventListener('DOMContentLoaded', loadHistory);
</script>

<?php include 'includes/footer.php'; ?>