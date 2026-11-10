(function () {
    const HOTLINE = '0987.654.321';
    const FAQ = [
        { k: ['giao hang', 'van chuyen', 'ship', 'phi giao'], a: 'NexTech giao hỏa tốc 2H trong nội thành, phí vận chuyển hiện là 30.000 đ cho mỗi đơn hàng.' },
        { k: ['doi tra', 'hoan tien', 'tra hang'], a: 'Sản phẩm được đổi trả trong 7 ngày nếu lỗi do nhà sản xuất, thủ tục 1 đổi 1.' },
        { k: ['bao hanh'], a: 'Tất cả sản phẩm chính hãng được bảo hành 12 tháng. Hotline khiếu nại và bảo hành: 1900.8899.' },
        { k: ['thanh toan', 'cod', 'chuyen khoan', 'momo', 'qr'], a: 'Bạn có thể thanh toán khi nhận hàng (COD), chuyển khoản hoặc Momo. Ở trang thanh toán, chọn chuyển khoản hoặc Momo để hiện mã QR.' },
        { k: ['voucher', 'giam gia', 'khuyen mai', 'ma giam'], a: 'Bạn xem tất cả mã giảm giá tại <a href="voucher.php">trang Voucher</a>, rồi nhập mã ở bước thanh toán.' },
        { k: ['don hang', 'lich su', 'theo doi'], a: 'Bạn xem đơn đã đặt tại <a href="history.php">Lịch sử mua hàng</a> (cần đăng nhập).' },
        { k: ['lien he', 'hotline', 'dien thoai', 'sdt'], a: 'Hotline tư vấn mua hàng: ' + HOTLINE + ' (08:00 - 22:00 hàng ngày).' },
        { k: ['xin chao', 'chao', 'hello', 'hi'], a: 'Xin chào! Mình có thể giúp bạn tìm sản phẩm, hỏi về giao hàng, thanh toán, đổi trả hay voucher.' }
    ];
    const QUICK = ['Phí giao hàng', 'Cách thanh toán', 'Đổi trả', 'Tìm iPhone'];

    const boDau = s => String(s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd').replace(/Đ/g, 'D').toLowerCase().trim();
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const vnd = n => new Intl.NumberFormat('vi-VN').format(Math.round(Number(n))) + ' đ';

    let products = null;
    async function layDanhSach() {
        if (products) return products;
        try {
            const res = await fetchAPI('/product/index.php');
            products = res.data || [];
        } catch (e) {
            products = [];
        }
        return products;
    }

    async function traLoi(text) {
        const q = boDau(text);
        for (const item of FAQ) {
            if (item.k.some(w => q.includes(w))) return item.a;
        }
        const list = await layDanhSach();
        const words = q.replace(/^(tim|tim kiem|mua|co|cho toi xem|xem)\s+/, '').split(/\s+/).filter(w => w.length > 1);
        let found = [];
        if (words.length) {
            found = list.filter(p => {
                const name = boDau(p.name);
                return words.every(w => name.includes(w));
            });
            if (!found.length) {
                found = list.filter(p => words.some(w => w.length > 2 && boDau(p.name).includes(w)));
            }
        }
        if (found.length) {
            const seen = new Set();
            const rows = found.filter(p => !seen.has(p.name) && seen.add(p.name)).slice(0, 4)
                .map(p => `<a href="product-detail.php?id=${p.id}">${esc(p.name)}</a> - ${vnd(p.price)}`);
            return 'Mình tìm thấy:<br>' + rows.join('<br>');
        }
        return 'Mình chưa hiểu rõ câu hỏi. Bạn thử hỏi về giao hàng, thanh toán, đổi trả, voucher hoặc gõ tên sản phẩm. Cần hỗ trợ ngay: ' + HOTLINE + '.';
    }

    function init() {
        const wrap = document.createElement('div');
        wrap.innerHTML = `
            <button type="button" class="nt-chat-btn" aria-label="Mở chat hỗ trợ"><i class="bi bi-chat-dots-fill"></i></button>
            <div class="nt-chat-box" role="dialog" aria-label="Chat hỗ trợ NexTech">
                <div class="nt-chat-head">
                    <div>Trợ lý NexTech<small>Thường trả lời ngay</small></div>
                    <button type="button" aria-label="Đóng">&times;</button>
                </div>
                <div class="nt-chat-log"></div>
                <div class="nt-chat-quick"></div>
                <form class="nt-chat-form">
                    <input type="text" placeholder="Nhập câu hỏi..." maxlength="200" autocomplete="off">
                    <button type="submit"><i class="bi bi-send-fill"></i></button>
                </form>
            </div>`;
        document.body.appendChild(wrap);

        const btn = wrap.querySelector('.nt-chat-btn');
        const box = wrap.querySelector('.nt-chat-box');
        const log = wrap.querySelector('.nt-chat-log');
        const quick = wrap.querySelector('.nt-chat-quick');
        const form = wrap.querySelector('.nt-chat-form');
        const input = form.querySelector('input');

        function them(cls, html) {
            const d = document.createElement('div');
            d.className = 'nt-msg ' + cls;
            d.innerHTML = html;
            log.appendChild(d);
            log.scrollTop = log.scrollHeight;
        }

        async function gui(text) {
            text = text.trim();
            if (!text) return;
            them('user', esc(text));
            input.value = '';
            them('bot', await traLoi(text));
        }

        QUICK.forEach(t => {
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = t;
            b.addEventListener('click', () => gui(t));
            quick.appendChild(b);
        });

        let daChao = false;
        function toggle(open) {
            box.classList.toggle('open', open);
            if (open && !daChao) {
                daChao = true;
                them('bot', 'Xin chào! Mình là trợ lý NexTech. Bạn cần tìm sản phẩm hay hỏi về chính sách?');
            }
            if (open) input.focus();
        }

        btn.addEventListener('click', () => toggle(!box.classList.contains('open')));
        box.querySelector('.nt-chat-head button').addEventListener('click', () => toggle(false));
        form.addEventListener('submit', e => { e.preventDefault(); gui(input.value); });
    }

    document.addEventListener('DOMContentLoaded', init);
})();
