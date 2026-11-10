<?php
$pageTitle = 'Thanh toán';
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

<script src="assets/js/vendor/qrcode.js"></script>
<script src="assets/js/payment-qr.js?v=1"></script>
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


const SHIPPING_FIELDS = ['receiver_name', 'receiver_phone', 'shipping_city', 'shipping_ward', 'shipping_address', 'note'];

// Đọc các giá trị khách đang gõ (null nếu form chưa được vẽ)
function readFormValues() {
    const form = document.getElementById('checkoutForm');
    if (!form) return null;
    const v = {};
    SHIPPING_FIELDS.forEach(n => { v[n] = form.elements[n]?.value ?? ''; });
    return v;
}

// Điền form: ưu tiên dữ liệu đang gõ, nếu chưa có thì gợi ý từ đơn gần nhất của chính khách
async function fillShippingForm(prev) {
    let values = prev;
    if (!values || SHIPPING_FIELDS.every(n => !values[n])) {
        try {
            const r = await fetchAPI('/order/last-shipping.php');
            values = r.data || {};
        } catch (e) {
            values = {};
        }
    }
    const form = document.getElementById('checkoutForm');
    SHIPPING_FIELDS.forEach(n => {
        if (form.elements[n] && values[n]) form.elements[n].value = values[n];
    });
}

// Trả về {field, message} cho ô đầu tiên không hợp lệ, hoặc null nếu hợp lệ
function validateShipping(form) {
    const el = n => form.elements[n];
    const phone = el('receiver_phone').value.replace(/[\s.\-()]/g, '')
        .replace(/^\+84/, '0').replace(/^84(?=\d{9}$)/, '0');
    if (el('receiver_name').value.trim().length < 2)
        return { field: el('receiver_name'), message: 'Vui lòng nhập họ và tên người nhận' };
    if (!/^0\d{9}$/.test(phone))
        return { field: el('receiver_phone'), message: 'Số điện thoại không hợp lệ (gồm 10 chữ số, bắt đầu bằng 0)' };
    if (el('shipping_city').value.trim().length < 2)
        return { field: el('shipping_city'), message: 'Vui lòng nhập Tỉnh / Thành phố' };
    if (el('shipping_ward').value.trim().length < 2)
        return { field: el('shipping_ward'), message: 'Vui lòng nhập Phường / Xã' };
    if (el('shipping_address').value.trim().length < 5)
        return { field: el('shipping_address'), message: 'Vui lòng nhập địa chỉ cụ thể (số nhà, tên đường...)' };
    return null;
}

let checkoutTotal = 0;

function capNhatQR() {
    const box = document.getElementById('paymentQR');
    const method = document.querySelector('select[name="payment_method"]')?.value;
    if (!box) return;
    if (method === 'COD' || !checkoutTotal) {
        box.innerHTML = '';
        return;
    }
    hienQRThanhToan(box, checkoutTotal, PAYMENT_CONFIG.memoPrefix + ' THANH TOAN', 'Checkout');
}

async function loadCheckout() {
    const container = document.getElementById('checkoutContent');
    // Giữ lại phương thức thanh toán đã chọn khi vẽ lại trang (sau khi áp/gỡ mã)
    const prevPayment = document.querySelector('select[name="payment_method"]')?.value;
    // Giữ lại những gì khách đã gõ trong form giao hàng khi trang vẽ lại
    const prevForm = readFormValues();
    
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
                <div class="col-lg-5 order-lg-1 mb-4">
                    <div class="card shadow-sm checkout-summary-card">
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
                <div class="col-lg-7 order-lg-2">
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

                    <form id="checkoutForm" novalidate>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"> 📍 Thông tin giao hàng </h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="receiver_name">Họ và tên người nhận <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="receiver_name" name="receiver_name"
                                           maxlength="100" autocomplete="name" placeholder="Nguyễn Văn A" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="receiver_phone">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="receiver_phone" name="receiver_phone"
                                           maxlength="15" inputmode="tel" autocomplete="tel" placeholder="0901234567" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="shipping_city">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shipping_city" name="shipping_city"
                                           maxlength="100" autocomplete="address-level1" placeholder="TP. Hồ Chí Minh" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="shipping_ward">Phường / Xã <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shipping_ward" name="shipping_ward"
                                           maxlength="100" autocomplete="address-level2" placeholder="Phường Bến Nghé" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="shipping_address">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shipping_address" name="shipping_address"
                                           maxlength="255" autocomplete="street-address" placeholder="Số nhà, tên đường, tòa nhà..." required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="note">Ghi chú cho shop <small class="text-muted">(không bắt buộc)</small></label>
                                    <textarea class="form-control" id="note" name="note" rows="2" maxlength="500"
                                              placeholder="Ví dụ: giao giờ hành chính, gọi trước khi giao..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"> 💳 Phương thức thanh toán </h4>
                        </div>
                        <div class="card-body">
                            <select name="payment_method" class="form-select" aria-label="Phương thức thanh toán">
                                <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                                <option value="Banking">Chuyển khoản ngân hàng</option>
                                <option value="Momo">Ví Momo</option>
                            </select>
                            <div id="paymentQR"></div>
                            <div id="formError" class="alert alert-danger py-2 mt-3 mb-0 d-none" role="alert"></div>
                            <button type="submit" id="placeOrderBtn" class="btn btn-success btn-lg w-100 mt-3"> Xác nhận đặt hàng </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        `;
        
        if (prevPayment) {
            document.querySelector('select[name="payment_method"]').value = prevPayment;
        }
        checkoutTotal = Number(finalTotal) || 0;
        const paySelect = document.querySelector('select[name="payment_method"]');
        paySelect.addEventListener('change', capNhatQR);
        capNhatQR();
        await fillShippingForm(prevForm);

        if (res.data.voucher_message) {
            showToast(res.data.voucher_message, 'warning');
        }

        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const errBox = document.getElementById('formError');
            const btn = document.getElementById('placeOrderBtn');
            errBox.classList.add('d-none');

            // Kiểm tra bắt buộc ở phía trình duyệt (server vẫn kiểm tra lại)
            const problem = validateShipping(this);
            if (problem) {
                errBox.textContent = problem.message;
                errBox.classList.remove('d-none');
                problem.field.focus();
                return;
            }

            const formData = new FormData(this);
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            try {
                const orderRes = await fetchAPI('/order/checkout.php', {
                    method: 'POST',
                    body: Object.fromEntries(formData.entries())
                });
                
                const d = orderRes.data || {};
                container.innerHTML = `<div class="text-center py-5"><a href="order-detail.php?id=${encodeURIComponent(d.order_id)}" class="btn btn-outline-danger">Xem đơn hàng đã đặt</a></div>`;
                hienPopupDatHang({
                    orderId: d.order_id,
                    total: d.total,
                    discount: d.discount,
                    voucher: d.voucher_code,
                    payment: formData.get('payment_method')
                });
            } catch (err) {
                btn.disabled = false;
                btn.textContent = 'Xác nhận đặt hàng';
                if (err.message.includes('Auth') || err.message.includes('Unauthorized') || err.message.includes('Login') || err.message.includes('Chưa đăng nhập')) {
                    container.innerHTML = `
                        <div class='alert alert-danger text-center mt-4'>
                            Bạn cần <a href='login.php'>đăng nhập</a> để tiến hành đặt hàng!
                        </div>
                    `;
                } else {
                    errBox.textContent = err.message;
                    errBox.classList.remove('d-none');
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
