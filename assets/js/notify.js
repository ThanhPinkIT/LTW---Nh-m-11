// Thông báo nhỏ + popup (thêm giỏ hàng, đặt hàng thành công)

const ntEsc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const ntTien = n => Number(n || 0).toLocaleString('vi-VN') + ' đ';

function thongBao(noiDung, loai = 'success', ms = 2800) {
    let box = document.getElementById('ntToasts');
    if (!box) {
        box = document.createElement('div');
        box.id = 'ntToasts';
        box.className = 'nt-toasts';
        box.setAttribute('aria-live', 'polite');
        document.body.appendChild(box);
    }
    const t = document.createElement('div');
    t.className = 'nt-toast nt-' + loai;
    t.textContent = noiDung;
    t.addEventListener('click', dong);
    box.appendChild(t);

    function dong() {
        t.classList.add('nt-out');
        setTimeout(() => t.remove(), 250);
    }
    setTimeout(dong, ms);
}

// khung popup dùng chung
function moPopup({ nho, hero, tieuDe, moTa, noiDung, nutPhu, nutChinh, onClose }) {
    const lop = document.createElement('div');
    lop.className = 'nt-overlay';
    lop.innerHTML = `
        <div class="nt-modal${nho ? ' nt-modal--sm' : ''}" role="dialog" aria-modal="true" aria-labelledby="ntTitle">
            <button type="button" class="nt-close" aria-label="Đóng">&times;</button>
            <div class="nt-hero${nho ? ' nt-hero--sm' : ''}">${hero}</div>
            <div class="nt-body">
                <h3 id="ntTitle">${tieuDe}</h3>
                <p class="nt-sub">${moTa}</p>
                ${noiDung}
                <div class="nt-actions">
                    ${nutPhu.href
                        ? `<a class="nt-btn nt-ghost" href="${nutPhu.href}">${nutPhu.text}</a>`
                        : `<button type="button" class="nt-btn nt-ghost" data-dong>${nutPhu.text}</button>`}
                    <a class="nt-btn nt-main" href="${nutChinh.href}">${nutChinh.text}</a>
                </div>
            </div>
        </div>`;
    document.body.appendChild(lop);
    document.body.classList.add('nt-lock');

    const dong = () => {
        document.removeEventListener('keydown', phim);
        document.body.classList.remove('nt-lock');
        lop.remove();
        if (typeof onClose === 'function') onClose();
    };
    const phim = e => { if (e.key === 'Escape') dong(); };
    document.addEventListener('keydown', phim);
    lop.querySelector('.nt-close').addEventListener('click', dong);
    lop.querySelector('[data-dong]')?.addEventListener('click', dong);
    lop.addEventListener('click', e => { if (e.target === lop) dong(); });
    lop.querySelector('.nt-main').focus();
}

const HERO_TICK = `
    <svg class="nt-check" viewBox="0 0 52 52" aria-hidden="true">
        <circle class="nt-ring" cx="26" cy="26" r="23"/>
        <path class="nt-tick" d="M15 27l8 8 15-16"/>
    </svg>`;

const HERO_GIO = `
    <div class="nt-cart">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M2.5 3.5h2.6l2.1 11h10.2l2-8H6.2"/>
            <circle cx="9" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/>
        </svg>
        <span class="nt-badge">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 12.5l4 4 8-9"/></svg>
        </span>
    </div>`;

// popup đặt hàng thành công
function hienPopupDatHang(info = {}) {
    moPopup({
        hero: HERO_TICK,
        tieuDe: 'Đặt hàng thành công',
        moTa: 'Cảm ơn bạn đã mua sắm tại NexTech.<br>Đơn hàng của bạn đang được xử lý.',
        noiDung: `
            <div class="nt-receipt">
                <div class="nt-row"><span>Mã đơn hàng</span><strong>#${ntEsc(info.orderId)}</strong></div>
                ${info.voucher ? `<div class="nt-row"><span>Mã giảm giá ${ntEsc(info.voucher)}</span><strong>- ${ntTien(info.discount)}</strong></div>` : ''}
                <div class="nt-row nt-total"><span>Tổng thanh toán</span><strong>${ntTien(info.total)}</strong></div>
            </div>`,
        nutPhu: { text: 'Tiếp tục mua sắm', href: info.tiepTuc || 'products.php' },
        nutChinh: { text: 'Xem đơn hàng', href: info.orderId ? 'order-detail.php?id=' + encodeURIComponent(info.orderId) : 'history.php' },
        onClose: info.onClose
    });
}

// popup thêm vào giỏ hàng thành công
function hienPopupGioHang(info = {}) {
    moPopup({
        nho: true,
        hero: HERO_GIO,
        tieuDe: 'Đã thêm vào giỏ hàng',
        moTa: info.tong ? `Giỏ hàng của bạn hiện có <strong>${ntEsc(info.tong)}</strong> sản phẩm.` : 'Sản phẩm đã được thêm vào giỏ hàng.',
        noiDung: info.ten ? `
            <div class="nt-item">
                ${info.anh ? `<img src="${ntEsc(info.anh)}" alt="" onerror="this.remove()">` : ''}
                <div class="nt-item-info">
                    <div class="nt-item-name">${ntEsc(info.ten)}</div>
                    ${info.gia ? `<div class="nt-item-price">${ntEsc(info.gia)}</div>` : ''}
                </div>
            </div>` : '',
        nutPhu: { text: 'Tiếp tục mua sắm' },
        nutChinh: { text: 'Xem giỏ hàng', href: 'cart.php' }
    });
}
