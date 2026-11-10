(function () {
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const vnd = n => Math.round(Number(n)).toLocaleString('vi-VN') + ' đ';

    async function init() {
        const box = document.getElementById('sideAd');
        if (!box || typeof fetchAPI !== 'function' || window.innerWidth < 1500) return;

        let list = [];
        try {
            list = (await fetchAPI('/product/index.php')).data || [];
        } catch (e) {
            return;
        }

        const giam = p => Number(p.original_price) > Number(p.price) ? 1 - Number(p.price) / Number(p.original_price) : 0;
        const top = list.filter(p => giam(p) > 0).sort((a, b) => giam(b) - giam(a)).slice(0, 5);
        const p = top[Math.floor(Math.random() * top.length)];
        if (!p) return;

        const file = decodeURIComponent(String(p.image || '').split('/').pop());
        const anh = file ? 'uploads/' + encodeURIComponent(file) : '';
        const link = 'product-detail.php?id=' + encodeURIComponent(p.id);
        const pct = Math.round(giam(p) * 100);

        box.innerHTML = `
            <div class="side-ad-card">
                <div class="side-ad-label">Giảm ${pct}%</div>
                <button type="button" class="side-ad-close" aria-label="Đóng quảng cáo">&times;</button>
                <a class="side-ad-img" href="${link}"><img src="${anh}" alt="${esc(p.name)}"></a>
                <div class="side-ad-body">
                    <a class="side-ad-name" href="${link}">${esc(p.name)}</a>
                    <div class="side-ad-price">${vnd(p.price)}</div>
                    <div class="side-ad-old">${vnd(p.original_price)}</div>
                    <a class="side-ad-btn" href="${link}">Xem ngay</a>
                </div>
            </div>`;
        box.querySelector('.side-ad-close').addEventListener('click', () => box.remove());
        box.hidden = false;
    }

    document.addEventListener('DOMContentLoaded', init);
})();
