// nguon: nút bấm (để lấy tên, ảnh, giá của sản phẩm hiện trong popup)
async function addToCart(productId, nguon) {
    const card = nguon && nguon.closest ? nguon.closest('.product-store-card') : null;
    const info = {
        ten: card?.querySelector('.product-card-title')?.textContent.trim(),
        anh: card?.querySelector('.product-thumb-img')?.currentSrc,
        gia: card?.querySelector('.price-current')?.textContent.trim()
    };

    try {
        const result = await fetchAPI('/cart/add.php', {
            method: 'POST',
            body: { product_id: productId, quantity: 1 }
        });
        info.tong = capNhatSoLuongGio(result.data);
        hienPopupGioHang(info);
    } catch (error) {
        thongBao(error.message || 'Không thể thêm vào giỏ hàng', 'danger');
    }
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
