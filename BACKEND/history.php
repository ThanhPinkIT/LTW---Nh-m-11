<?php
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

function getStatusBadge(status) {
    switch (status) {
        case 'Pending': return '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
        case 'Processing': return '<span class="badge bg-info">Đang giao</span>';
        case 'Completed': return '<span class="badge bg-success">Hoàn thành</span>';
        case 'Cancelled': return '<span class="badge bg-danger">Đã hủy</span>';
        default: return `<span class="badge bg-secondary">${status}</span>`;
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
            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td><span class="fw-bold text-primary">#${order.id}</span></td>
                    <td>${date}</td>
                    <td class="text-danger fw-bold">${formatVND(order.total_money)}</td>
                    <td>${order.payment_method}</td>
                    <td>${getStatusBadge(order.status)}</td>
                </tr>
            `;
        });
        
        container.innerHTML = `
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover table-bordered align-middle text-center bg-white mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>STT</th>
                            <th>Mã Đơn Hàng</th>
                            <th>Ngày Đặt</th>
                            <th>Tổng Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows}
                    </tbody>
                </table>
            </div>
        `;
        
    } catch (err) {
        if (err.message.includes('Auth') || err.message.includes('Login')) {
            window.location.href = 'login.php';
        } else {
            container.innerHTML = `<div class="alert alert-danger">Lỗi tải lịch sử đơn hàng: ${err.message}</div>`;
        }
    }
}

document.addEventListener('DOMContentLoaded', loadHistory);
</script>

<?php include 'includes/footer.php'; ?>