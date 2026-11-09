<?php
include 'includes/header.php';
?>
<div class="container mt-4 cart-page">
    <h2 class="mb-4"> 🛒 Giỏ hàng của bạn </h2>
    
    <!-- Notification -->
    <div id="toastMessage" class="alert d-none fixed-top mt-3 mx-auto shadow" style="max-width: 400px; z-index: 9999;"></div>

    <div id="cartContent">
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<script>
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
const esc = (t) => String(t ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toastMessage');
    toast.className = `alert alert-${type} fixed-top mt-3 mx-auto shadow`;
    toast.textContent = msg;
    toast.classList.remove('d-none');
    setTimeout(() => toast.classList.add('d-none'), 3000);
}

async function loadCart() {
    const container = document.getElementById('cartContent');
    
    try {
        const res = await fetchAPI('/cart/index.php');
        const items = res.data.items;
        const total = res.data.total;
        // Số liệu có voucher do server tính (fallback về total nếu backend cũ)
        const subtotal = res.data.subtotal ?? total;
        const shipping = res.data.shipping_fee ?? 0;
        const discount = res.data.discount ?? 0;
        const finalTotal = res.data.final_total ?? total;
        const voucher = res.data.voucher;
        
        if (!items || items.length === 0) {
            container.innerHTML = `
                <div class="alert cart-empty-message"> Chưa có đơn hàng. </div>
                <a href="products.php" class="btn btn-primary"> Xem sản phẩm </a>
            `;
            return;
        }
        
        let rows = '';
        items.forEach(item => {
            const parts = item.image.split('/');
            const imgName = parts[parts.length - 1];
            rows += `
                <tr>
                    <td>
                        <img src="uploads/${imgName}" width="80" class="img-thumbnail" onerror="this.src='https://placehold.co/400x400/eeeeee/999999?text=No+Image';">
                    </td>
                    <td>${item.name}</td>
                    <td class="text-danger fw-bold">${formatVND(item.price)}</td>
                    <td>
                        <input type="number" id="qty-${item.id}" value="${item.quantity}" min="1" class="form-control text-center">
                    </td>
                    <td>${formatVND(item.subtotal)}</td>
                    <td>
                        <button onclick="updateCart(${item.id})" class="btn btn-warning btn-sm">Cập nhật</button>
                        <button onclick="removeItem(${item.id})" class="btn btn-danger btn-sm"> Xóa </button>
                    </td>
                </tr>
            `;
        });
        
        container.innerHTML = `
            <table class="table table-bordered align-middle text-center cart-table">
                <thead class="cart-table-head">
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
            <tbody>
                ${rows}
            <tr>
                <td colspan="4" class="text-end">Tạm tính</td>
                <td colspan="2">${formatVND(subtotal)}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end">Phí vận chuyển</td>
                <td colspan="2">${formatVND(shipping)}</td>
            </tr>
            ${discount > 0 ? `
            <tr class="text-success">
                <td colspan="4" class="text-end">Giảm giá (${esc(voucher ? voucher.code : '')})</td>
                <td colspan="2">-${formatVND(discount)}</td>
            </tr>` : ''}
            <tr>
                <td colspan="4" class="text-end fw-bold">Tổng thanh toán</td>
                <td colspan="2" class="text-danger fw-bold fs-5">${formatVND(finalTotal)}</td>
            </tr>
        </tbody>
    </table>

    <div class="card mb-3">
        <div class="card-body">
            <label class="form-label fw-bold" for="voucherInput">🎟️ Mã giảm giá</label>
            <div class="input-group mb-1">
                <input type="text" id="voucherInput" class="form-control" placeholder="Nhập mã giảm giá"
                       value="${voucher ? esc(voucher.code) : ''}">
                <button type="button" class="btn btn-danger" onclick="applyVoucher()">Áp dụng</button>
                ${voucher ? '<button type="button" class="btn btn-outline-secondary" onclick="removeVoucher()">Gỡ mã</button>' : ''}
            </div>
            <a href="voucher.php" class="small">Xem tất cả voucher</a>
        </div>
    </div>

    <div class="d-flex justify-content-between cart-action-bar">
        <a href="products.php" class="btn cart-continue-btn">← Tiếp tục mua hàng</a>
        <div>
            <a href="checkout.php" class="btn cart-checkout-btn">Thanh toán</a>
        </div>
    </div>
`;

        if (res.data.voucher_message) {
            showToast(res.data.voucher_message, 'warning');
        }

    } catch (err) {
        container.innerHTML = `
            <div class="alert alert-danger">
                Lỗi tải giỏ hàng: ${err.message}
            </div>
        `;
    }
}

async function updateCart(id) {
    const qty = document.getElementById(`qty-${id}`).value;
    try {
        await fetchAPI('/cart/update.php', {
            method: 'POST',
            body: { product_id: id, quantity: qty }
        });
        loadCart();
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

async function applyVoucher() {
    const code = document.getElementById('voucherInput').value.trim();
    if (!code) {
        showToast('Vui lòng nhập mã giảm giá', 'warning');
        return;
    }
    try {
        const r = await fetchAPI('/voucher/apply.php', {
            method: 'POST',
            body: { code: code }
        });
        await loadCart();
        showToast(r.message);
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

async function removeVoucher() {
    try {
        await fetchAPI('/voucher/remove.php', { method: 'POST', body: {} });
        await loadCart();
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

async function removeItem(id) {
    try {
        await fetchAPI('/cart/remove.php', {
            method: 'POST',
            body: { product_id: id }
        });
        loadCart();
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', loadCart);
</script>

<?php include 'includes/footer.php'; ?>
