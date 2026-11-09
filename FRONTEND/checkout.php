<?php
include 'includes/header.php';
?>
<div class="container mt-4 checkout-page">
    <!-- Notification -->
    <div id="toastMessage" class="alert d-none fixed-top mt-3 mx-auto shadow" style="max-width: 400px; z-index: 9999;"></div>

    <div id="checkoutContent">
        <div class="col-12 text-center mt-5">
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

async function loadCheckout() {
    const container = document.getElementById('checkoutContent');
    // Giữ lại phương thức thanh toán đã chọn khi vẽ lại trang (sau khi áp/gỡ mã)
    const prevPayment = document.querySelector('select[name="payment_method"]')?.value;
    
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
                <div class='alert alert-warning text-center mt-4'>
                    Giỏ hàng của bạn đang trống!
                    <a href='index.php'>Mua sắm ngay</a>
                </div>
            `;
            return;
        }
        
        let rows = '';
        items.forEach(item => {
            rows += `
                <tr>
                    <td>${item.name}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end text-danger fw-bold">${formatVND(item.subtotal)}</td>
                </tr>
            `;
        });
        
        container.innerHTML = `
            <div class="row">
                <div class="col-md-7">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"> 🎟️ Mã giảm giá </h5>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-1">
                                <input type="text" id="voucherInput" class="form-control" placeholder="Nhập mã giảm giá"
                                       value="${voucher ? esc(voucher.code) : ''}">
                                <button type="button" class="btn btn-danger" onclick="applyVoucher()">Áp dụng</button>
                                ${voucher ? '<button type="button" class="btn btn-outline-secondary" onclick="removeVoucher()">Gỡ mã</button>' : ''}
                            </div>
                            ${voucher ? `<div class="text-success small">Đã áp dụng ${esc(voucher.code)}: ${esc(voucher.description || '')}</div>` : ''}
                            <a href="voucher.php" class="small">Xem tất cả voucher</a>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"> Thông tin thanh toán </h4>
                        </div>
                        <div class="card-body">
                            <form id="checkoutForm">
                                <div class="mb-3">
                                    <label class="form-label"> Phương thức thanh toán </label>
                                    <select name="payment_method" class="form-select">
                                        <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                                        <option value="Banking">Chuyển khoản ngân hàng</option>
                                        <option value="Momo">Ví Momo</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg w-100 mt-3"> Xác nhận đặt hàng </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h4 class="mb-0"> Tóm tắt đơn hàng </h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end">Tạm tính:</td>
                                        <td class="text-end">${formatVND(subtotal)}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end">Phí vận chuyển:</td>
                                        <td class="text-end">${formatVND(shipping)}</td>
                                    </tr>
                                    ${discount > 0 ? `
                                    <tr class="text-success">
                                        <td colspan="2" class="text-end">Giảm giá (${esc(voucher ? voucher.code : '')}):</td>
                                        <td class="text-end">-${formatVND(discount)}</td>
                                    </tr>` : ''}
                                    <tr class="table-warning">
                                        <th colspan="2" class="text-end fs-5"> Tổng thanh toán: </th>
                                        <th class="text-end text-danger fs-5 fw-bold">${formatVND(finalTotal)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        if (prevPayment) {
            document.querySelector('select[name="payment_method"]').value = prevPayment;
        }

        if (res.data.voucher_message) {
            showToast(res.data.voucher_message, 'warning');
        }

        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const orderRes = await fetchAPI('/order/checkout.php', {
                    method: 'POST',
                    body: Object.fromEntries(formData.entries())
                });
                
                const d = orderRes.data || {};
                container.innerHTML = `<div class="text-center py-5"><a href="history.php" class="btn btn-outline-danger">Xem đơn hàng đã đặt</a></div>`;
                hienPopupDatHang({
                    orderId: d.order_id,
                    total: d.total,
                    discount: d.discount,
                    voucher: d.voucher_code
                });
            } catch (err) {
                if (err.message.includes('Auth') || err.message.includes('Unauthorized') || err.message.includes('Login') || err.message.includes('Chưa đăng nhập')) {
                    container.innerHTML = `
                        <div class='alert alert-danger text-center mt-4'>
                            Bạn cần <a href='login.php'>đăng nhập</a> để tiến hành đặt hàng!
                        </div>
                    `;
                } else {
                    showToast(err.message, 'danger');
                    // Voucher không còn hợp lệ -> tải lại để cập nhật số tiền
                    if (/giảm giá/i.test(err.message)) {
                        loadCheckout();
                    }
                }
            }
        });
        
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">Lỗi kết nối API: ${err.message}</div>`;
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
        await loadCheckout();
        showToast(r.message);
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

async function removeVoucher() {
    try {
        await fetchAPI('/voucher/remove.php', { method: 'POST', body: {} });
        await loadCheckout();
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', loadCheckout);
</script>

<?php include 'includes/footer.php'; ?>
