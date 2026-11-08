// voucher.js - Trang voucher, nối với BACKEND/api (dùng fetchAPI từ api.js)
document.addEventListener('DOMContentLoaded', function () {
    const voucherInput = document.getElementById('voucherCode');
    const voucherMessage = document.getElementById('voucherMessage');
    const applyButton = document.getElementById('applyVoucherBtn');
    const removeButton = document.getElementById('removeVoucherBtn');
    const checkoutButton = document.getElementById('voucherCheckoutBtn');
    const voucherList = document.getElementById('voucherList');

    const formatMoney = value =>
        Number(value || 0).toLocaleString('vi-VN') + 'đ';

    // Chống chèn HTML từ dữ liệu do admin nhập
    const esc = text => String(text ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    function setMessage(text, type) {
        voucherMessage.textContent = text || '';
        voucherMessage.className = 'voucher-message' + (type ? ' ' + type : '');
    }

    // Cập nhật phần tổng kết theo số liệu giỏ hàng từ server
    function renderSummary(cart) {
        document.getElementById('subtotal').textContent =
            formatMoney(cart.subtotal);

        document.getElementById('shippingFee').textContent =
            formatMoney(cart.shipping_fee);

        document.getElementById('discountAmount').textContent =
            cart.discount > 0 ? '-' + formatMoney(cart.discount) : '0đ';

        document.getElementById('totalAmount').textContent =
            formatMoney(cart.final_total);

        if (cart.voucher) {
            voucherInput.value = cart.voucher.code;
            removeButton.hidden = false;
        } else {
            removeButton.hidden = true;
        }
    }

    async function loadCart() {
        try {
            const res = await fetchAPI('/cart/index.php');
            const cart = res.data;
            renderSummary(cart);

            if (cart.voucher_message) {
                setMessage(cart.voucher_message, 'error');
            } else if (cart.voucher) {
                setMessage('Đang áp dụng mã ' + cart.voucher.code, 'success');
            } else if (!cart.items || cart.items.length === 0) {
                setMessage('Giỏ hàng đang trống, hãy thêm sản phẩm trước khi áp mã.', '');
            }
        } catch (err) {
            setMessage(err.message, 'error');
        }
    }

    function voucherDetail(v) {
        const parts = [];
        if (v.min_order > 0) parts.push('Đơn tối thiểu ' + formatMoney(v.min_order));
        if (v.end_date) parts.push('HSD: ' + new Date(v.end_date.replace(' ', 'T')).toLocaleDateString('vi-VN'));
        if (v.remaining !== null && v.remaining !== undefined) parts.push('Còn ' + v.remaining + ' lượt');
        return parts.join(' · ');
    }

    async function loadVouchers() {
        try {
            const res = await fetchAPI('/voucher/index.php');
            const vouchers = res.data;

            if (!vouchers || vouchers.length === 0) {
                voucherList.innerHTML =
                    '<p class="text-muted mb-0">Hiện chưa có voucher nào.</p>';
                return;
            }

            voucherList.innerHTML = vouchers.map(v => {
                const detail = voucherDetail(v);
                return `
                    <div class="voucher-item">
                        <div class="voucher-info">
                            <strong>Mã: ${esc(v.code)}</strong>
                            <p>${esc(v.description || '')}</p>
                            ${detail ? `<p><small>${esc(detail)}</small></p>` : ''}
                        </div>
                        <button type="button"
                                class="choose-voucher-btn"
                                data-code="${esc(v.code)}"
                                ${v.available ? '' : 'disabled'}>
                            ${v.available ? 'Chọn' : 'Đã dùng'}
                        </button>
                    </div>
                `;
            }).join('');
        } catch (err) {
            voucherList.innerHTML =
                '<p class="text-danger mb-0">Không tải được voucher: ' + esc(err.message) + '</p>';
        }
    }

    async function applyCode(code) {
        code = (code || '').trim().toUpperCase();

        if (!code) {
            setMessage('Vui lòng nhập mã giảm giá', 'error');
            return;
        }

        try {
            const res = await fetchAPI('/voucher/apply.php', {
                method: 'POST',
                body: { code: code }
            });

            voucherInput.value = code;
            renderSummary(res.data);
            setMessage(res.message, 'success');
        } catch (err) {
            setMessage(err.message, 'error');
        }
    }

    // Bấm "Chọn" trên một voucher -> điền mã và áp dụng luôn
    voucherList.addEventListener('click', function (e) {
        const button = e.target.closest('.choose-voucher-btn');
        if (!button || button.disabled) return;

        voucherInput.value = button.dataset.code;
        applyCode(button.dataset.code);
    });

    applyButton.addEventListener('click', function () {
        applyCode(voucherInput.value);
    });

    voucherInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') applyCode(voucherInput.value);
    });

    removeButton.addEventListener('click', async function () {
        try {
            const res = await fetchAPI('/voucher/remove.php', {
                method: 'POST',
                body: {}
            });

            voucherInput.value = '';
            renderSummary(res.data);
            setMessage(res.message, '');
        } catch (err) {
            setMessage(err.message, 'error');
        }
    });

    checkoutButton.addEventListener('click', function () {
        window.location.href = 'checkout.php';
    });

    loadVouchers();
    loadCart();
});
