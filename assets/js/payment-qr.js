const PAYMENT_CONFIG = {
    bankBin: '970422',
    bankName: 'MB Bank',
    accountNo: '0123456789',
    accountName: 'CONG TY NEXTECH',
    memoPrefix: 'NEXTECH'
};

function crc16(str) {
    let crc = 0xFFFF;
    for (let i = 0; i < str.length; i++) {
        crc ^= str.charCodeAt(i) << 8;
        for (let j = 0; j < 8; j++) {
            crc = (crc & 0x8000) ? ((crc << 1) ^ 0x1021) & 0xFFFF : (crc << 1) & 0xFFFF;
        }
    }
    return crc.toString(16).toUpperCase().padStart(4, '0');
}

function tlv(id, value) {
    return id + String(value.length).padStart(2, '0') + value;
}

function boDauNganHang(s) {
    return String(s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd').replace(/Đ/g, 'D').replace(/[^A-Za-z0-9 ]/g, '').trim().slice(0, 25);
}

function taoNoiDungVietQR(amount, memo) {
    const c = PAYMENT_CONFIG;
    const account = tlv('00', 'A000000727') + tlv('01', tlv('00', c.bankBin) + tlv('01', c.accountNo)) + tlv('02', 'QRIBFTTA');
    let payload = tlv('00', '01')
        + tlv('01', '12')
        + tlv('38', account)
        + tlv('53', '704');
    const money = Math.round(Number(amount) || 0);
    if (money > 0) payload += tlv('54', String(money));
    payload += tlv('58', 'VN');
    const note = boDauNganHang(memo);
    if (note) payload += tlv('62', tlv('08', note));
    payload += '6304';
    return payload + crc16(payload);
}

function veMaQR(el, amount, memo) {
    const qr = qrcode(0, 'M');
    qr.addData(taoNoiDungVietQR(amount, memo));
    qr.make();
    el.innerHTML = qr.createSvgTag({ cellSize: 5, margin: 2, scalable: true });
    const svg = el.querySelector('svg');
    if (svg) {
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', '100%');
    }
}

function htmlKhungQR(amount, memo, idSuffix) {
    const c = PAYMENT_CONFIG;
    const fmt = new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' đ';
    return `
        <div class="pay-qr-box">
            <div class="pay-qr-img" id="payQrImg${idSuffix}"></div>
            <div class="pay-qr-info">
                <div class="pay-qr-title">Quét mã để thanh toán</div>
                <div class="pay-qr-line"><span>Ngân hàng</span><strong>${c.bankName}</strong></div>
                <div class="pay-qr-line"><span>Số tài khoản</span><strong>${c.accountNo}</strong></div>
                <div class="pay-qr-line"><span>Chủ tài khoản</span><strong>${c.accountName}</strong></div>
                <div class="pay-qr-line"><span>Số tiền</span><strong class="text-danger">${fmt}</strong></div>
                <div class="pay-qr-line"><span>Nội dung</span><strong>${memo}</strong></div>
                <div class="pay-qr-hint">Mở app ngân hàng hoặc Momo, chọn quét QR (VietQR) rồi xác nhận chuyển khoản.</div>
            </div>
        </div>`;
}

function hienQRThanhToan(container, amount, memo, idSuffix = '') {
    container.innerHTML = htmlKhungQR(amount, memo, idSuffix);
    veMaQR(container.querySelector('#payQrImg' + idSuffix), amount, memo);
}
