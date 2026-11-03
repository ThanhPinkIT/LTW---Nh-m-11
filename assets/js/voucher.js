
document.addEventListener('DOMContentLoaded', function () {
    const voucherInput = document.getElementById('voucherCode');
    const voucherMessage = document.getElementById('voucherMessage');
    const applyButton = document.getElementById('applyVoucherBtn');
    const checkoutButton = document.getElementById('voucherCheckoutBtn');

    // Dữ liệu minh họa FE, chưa liên kết giỏ hàng thật
    const subtotal = 500000;
    const shippingFee = 30000;

    const formatMoney = value =>
        value.toLocaleString('vi-VN') + 'đ';

    document.querySelectorAll('.choose-voucher-btn').forEach(button => {
        button.addEventListener('click', function () {
            voucherInput.value = this.dataset.code;
            voucherMessage.textContent = '';
            voucherMessage.className = 'voucher-message';
        });
    });

    applyButton.addEventListener('click', function () {
        const code = voucherInput.value.trim().toUpperCase();
        let discount = 0;

        switch (code) {
            case 'GIAM20':
                discount = Math.min(subtotal * 0.20, 100000);
                break;

            case 'FREESHIP':
                discount = Math.min(shippingFee, 30000);
                break;

            case 'TECH50K':
                discount = 50000;
                break;

            default:
                voucherMessage.textContent =
                    'Mã giảm giá không hợp lệ!';
                voucherMessage.className = 'voucher-message error';
                return;
        }

        const total = Math.max(
            0,
            subtotal + shippingFee - discount
        );

        document.getElementById('discountAmount').textContent =
            '-' + formatMoney(discount);

        document.getElementById('totalAmount').textContent =
            formatMoney(total);

        voucherMessage.textContent =
            'Đã áp dụng mã ' + code + ' (minh họa FE)';
        voucherMessage.className = 'voucher-message success';
    });

    checkoutButton.addEventListener('click', function () {
        voucherMessage.textContent =
            'Chức năng thanh toán cần tích hợp giỏ hàng và Backend.';
        voucherMessage.className = 'voucher-message error';
    });
});
