// thêm vào giỏ + hiện popup, dùng chung cho trang chủ, danh sách và trang chi tiết
// info: { ten, anh, gia } để hiện trong popup
async function themVaoGio(productId, soLuong, info = {}) {
    try {
        const result = await fetchAPI('/cart/add.php', {
            method: 'POST',
            body: { product_id: productId, quantity: soLuong }
        });
        info.tong = capNhatSoLuongGio(result.data);
        hienPopupGioHang(info);
        return true;
    } catch (error) {
        thongBao(error.message || 'Không thể thêm vào giỏ hàng', 'danger');
        return false;
    }
}

// nguon: nút bấm (để lấy tên, ảnh, giá của sản phẩm hiện trong popup)
function addToCart(productId, nguon) {
    const card = nguon && nguon.closest ? nguon.closest('.product-store-card') : null;
    return themVaoGio(productId, 1, {
        ten: card?.querySelector('.product-card-title')?.textContent.trim(),
        anh: card?.querySelector('.product-thumb-img')?.currentSrc,
        gia: card?.querySelector('.price-current')?.textContent.trim()
    });
}

// cập nhật số trên nút Giỏ hàng, không cần tải lại trang
function capNhatSoLuongGio(gio) {
    const tong = Object.values(gio || {}).reduce((t, n) => t + Number(n), 0);
    const link = document.querySelector('.navbar a[href="cart.php"]');
    if (link) {
        let badge = link.querySelector('.badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'badge bg-danger ms-1';
            link.appendChild(badge);
        }
        badge.textContent = tong;
    }
    return tong;
}

// ===== Tô nền khung ảnh theo màu nền của chính ảnh =====
// Ảnh sản phẩm thường có nền trắng/đen, khung lại màu xám nên hai bên bị lộ dải màu.
// Lấy màu ở 4 góc của ảnh rồi tô cho khung chứa ảnh để liền một khối.
function tuDongNenAnh(img) {
    const khung = img.closest('.product-thumb-wrap, .gallery-stage');
    if (!khung || !img.naturalWidth) return;

    let mau = '#fff';
    try {
        const c = document.createElement('canvas');
        c.width = c.height = 10;
        const g = c.getContext('2d', { willReadFrequently: true });
        g.drawImage(img, 0, 0, 10, 10);

        let r = 0, gr = 0, b = 0, n = 0;
        [[0, 0], [9, 0], [0, 9], [9, 9]].forEach(([x, y]) => {
            const d = g.getImageData(x, y, 1, 1).data;
            if (d[3] < 128) return;           // góc trong suốt thì bỏ qua
            r += d[0]; gr += d[1]; b += d[2]; n++;
        });
        if (n) mau = `rgb(${Math.round(r / n)}, ${Math.round(gr / n)}, ${Math.round(b / n)})`;
    } catch (e) {
        // ảnh khác nguồn không đọc được pixel -> giữ nền trắng
    }
    khung.style.backgroundColor = mau;
}

// ảnh nào tải xong (kể cả ảnh tạo sau bằng JS) cũng được xử lý
document.addEventListener('load', e => {
    if (e.target.matches && e.target.matches('.product-thumb-img, .gallery-stage img')) tuDongNenAnh(e.target);
}, true);
// ảnh đã tải xong trước khi file này chạy
document.querySelectorAll('.product-thumb-img, .gallery-stage img').forEach(img => {
    if (img.complete) tuDongNenAnh(img);
});
