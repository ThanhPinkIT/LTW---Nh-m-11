<?php
include 'includes/header.php';
?>
<div class="container mt-5">
    <div class="row" id="productDetail">
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<script src="assets/js/cart.js?v=6"></script>
<script>
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

let currentProduct = null;   // sản phẩm đang xem, dùng cho popup thêm giỏ hàng
let galleryImages = [];
let galleryIndex = 0;

const NO_IMAGE = 'https://placehold.co/400x400/eeeeee/999999?text=No+Image';

// đổi đường dẫn ảnh từ API thành uploads/<tên file> giống các trang khác
function toImageUrl(path) {
    const name = String(path || '').split('/').pop() || '';
    return name ? `uploads/${encodeURIComponent(name)}` : NO_IMAGE;
}

// hiện ảnh thứ i trong thư viện
function showGalleryImage(i) {
    const n = galleryImages.length;
    if (!n) return;
    galleryIndex = (i + n) % n;   // đi hết ảnh cuối thì quay về ảnh đầu

    const img = document.getElementById('galleryMain');
    img.src = galleryImages[galleryIndex];

    const counter = document.getElementById('galleryCounter');
    if (counter) counter.textContent = `${galleryIndex + 1} / ${n}`;

    document.querySelectorAll('.gallery-thumb').forEach((t, idx) => {
        t.classList.toggle('active', idx === galleryIndex);
    });
}

function initGallery() {
    if (galleryImages.length < 2) return;

    document.getElementById('galleryPrev').addEventListener('click', () => showGalleryImage(galleryIndex - 1));
    document.getElementById('galleryNext').addEventListener('click', () => showGalleryImage(galleryIndex + 1));
    document.querySelectorAll('.gallery-thumb').forEach((t, idx) => {
        t.addEventListener('click', () => showGalleryImage(idx));
    });

    // phím mũi tên trái / phải
    document.addEventListener('keydown', e => {
        if (e.target.tagName === 'INPUT') return;
        if (e.key === 'ArrowLeft') showGalleryImage(galleryIndex - 1);
        if (e.key === 'ArrowRight') showGalleryImage(galleryIndex + 1);
    });

    // vuốt trên điện thoại
    let startX = null;
    const box = document.getElementById('galleryBox');
    box.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    box.addEventListener('touchend', e => {
        if (startX === null) return;
        const dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 40) showGalleryImage(galleryIndex + (dx < 0 ? 1 : -1));
        startX = null;
    });
}

async function loadProductDetail() {
    const container = document.getElementById('productDetail');
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    if (!productId) {
        window.location.href = 'products.php';
        return;
    }

    try {
        const res = await fetchAPI(`/product/detail.php?id=${productId}`);
        const p = res.data;
        
        
        currentProduct = p;

        // gom ảnh: nếu API cũ chưa có images thì dùng ảnh chính
        const list = (p.images && p.images.length) ? p.images : [p.image];
        galleryImages = list.map(toImageUrl);
        galleryIndex = 0;
        const imageUrl = galleryImages[0];
        const nhieuAnh = galleryImages.length > 1;

        const galleryHTML = `
            <div class="gallery" id="galleryBox">
                <div class="gallery-stage">
                    <img id="galleryMain" src="${imageUrl}" alt="${escapeHTML(p.name)}"
                         onerror="this.onerror=null;this.src='${NO_IMAGE}';">
                    ${nhieuAnh ? `
                    <button type="button" class="gallery-btn gallery-prev" id="galleryPrev" aria-label="Ảnh trước">&lt;</button>
                    <button type="button" class="gallery-btn gallery-next" id="galleryNext" aria-label="Ảnh sau">&gt;</button>
                    <span class="gallery-counter" id="galleryCounter">1 / ${galleryImages.length}</span>` : ''}
                </div>
                ${nhieuAnh ? `
                <div class="gallery-thumbs">
                    ${galleryImages.map((u, i) => `
                        <img src="${u}" class="gallery-thumb${i === 0 ? ' active' : ''}" alt="Ảnh ${i + 1}"
                             onerror="this.remove()">`).join('')}
                </div>` : ''}
            </div>`;

        const safeName = escapeHTML(p.name);

        const descHTML = escapeHTML(p.description || '')
            .replace(/\r?\n/g, '<br>');

        // bảng thông số kỹ thuật (chỉ hiện khi sản phẩm có thông số)
        const specHTML = (p.specs && p.specs.length) ? `
            <div class="col-12">
                <div class="spec-card">
                    <h4>Thông số kỹ thuật</h4>
                    <table class="spec-table">
                        ${p.specs.map(s => `<tr><th>${escapeHTML(s.name)}</th><td>${escapeHTML(s.value)}</td></tr>`).join('')}
                    </table>
                </div>
            </div>` : '';


        container.innerHTML = `
            <div class="col-md-5">
                ${galleryHTML}
            </div>
            <div class="col-md-7">
                <h2> ${p.name} </h2>
                <h3 class="text-danger my-3">
                    ${formatVND(p.price)}
                </h3>
                <p>
                    <strong>Mô tả sản phẩm:</strong>
                </p>
                <p> ${descHTML} </p>
                
                <div class="d-flex mt-4 gap-3 align-items-center">
                    <div class="input-group" style="max-width:150px;">
                        <span class="input-group-text">SL</span>
                        <input type="number" id="qty" class="form-control text-center" value="1" min="1">
                    </div>
                    <button type="button" onclick="themVaoGioTuChiTiet()" class="btn btn-add-detail btn-lg"> <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng </button>
                </div>
            </div>
            
            ${specHTML}

            <div class="col-12 mt-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4">
                            ⭐ Đánh giá sản phẩm
                        </h4>

                        <div id="reviewList">
                            Đang tải đánh giá...
                        </div>
                    </div>
                </div>
            </div>

        `;
        initGallery();
        await loadReviews(productId);
    } catch (err) {
        container.innerHTML = `<div class="col-12"><div class="alert alert-danger">Lỗi tải chi tiết: ${err.message}</div></div>`;
    }
}


// bấm nút "Thêm vào giỏ hàng" ở trang chi tiết -> dùng popup chung (notify.js / cart.js)
async function themVaoGioTuChiTiet() {
    const qtyInput = document.getElementById('qty');
    const qty = Number(qtyInput.value);

    if (!Number.isSafeInteger(qty) || qty < 1) {
        thongBao('Số lượng phải là số nguyên lớn hơn 0.', 'danger');
        qtyInput.value = 1;
        return;
    }

    await themVaoGio(currentProduct.id, qty, {
        ten: currentProduct.name,
        anh: galleryImages[0],
        gia: formatVND(currentProduct.price)
    });
}


function escapeHTML(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function loadReviews(productId) {
    const reviewList = document.getElementById('reviewList');
    if (!reviewList) return;

    try {
        const res = await fetchAPI(
            `/product/reviews.php?id=${productId}`
        );

        const reviews = res.data || [];

        if (reviews.length === 0) {
            reviewList.innerHTML =
                '<p class="text-muted">Chưa có đánh giá.</p>';
            return;
        }

        const average = reviews.reduce(
            (sum, r) => sum + Number(r.rating), 0
        ) / reviews.length;

        let html = `
            <p>
                <strong>Điểm trung bình:</strong>
                <span class="text-warning">
                    ${'★'.repeat(Math.round(average))}
                </span>
                ${average.toFixed(1)}/5
                (${reviews.length} đánh giá)
            </p>
        `;

        reviews.forEach(r => {
            const rating = Math.max(
                0, Math.min(5, Math.round(Number(r.rating)))
            );

            html += `
                <div class="border-top py-3">
                    <strong>${escapeHTML(r.fullname)}</strong>
                    <div class="text-warning">
                        ${'★'.repeat(rating)}
                        ${'☆'.repeat(5 - rating)}
                    </div>
                    <p>${escapeHTML(r.comment)}</p>
                </div>
            `;
        });

        reviewList.innerHTML = html;

    } catch (err) {
        reviewList.innerHTML =
            '<p class="text-danger">Lỗi tải đánh giá.</p>';
    }
}

document.addEventListener('DOMContentLoaded', loadProductDetail);
</script>

<?php include 'includes/footer.php'; ?>