<?php
include 'includes/header.php';
?>
<div class="container mt-5">
    <!-- Notification -->
    <div id="toastMessage" class="alert d-none fixed-top mt-3 mx-auto shadow" style="max-width: 400px; z-index: 9999;"></div>

    <div class="row" id="productDetail">
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<script>
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toastMessage');
    toast.className = `alert alert-${type} fixed-top mt-3 mx-auto shadow`;
    toast.textContent = msg;
    toast.classList.remove('d-none');
    setTimeout(() => toast.classList.add('d-none'), 3000);
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
        
        const parts = p.image.split('/');
        const imgName = parts[parts.length - 1];

        // Format description with <br> instead of \n
        const descHTML = p.description.replace(/\n/g, '<br>');

        container.innerHTML = `
            <div class="col-md-5">
                <img
                    src="uploads/${imgName}"
                    class="img-fluid rounded shadow"
                    alt="${p.name}"
                    onerror="this.src='https://placehold.co/400x400/eeeeee/999999?text=No+Image';">
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
                    <button onclick="addToCart(${p.id})" class="btn btn-success btn-lg"> 🛒 Thêm vào giỏ hàng </button>
                </div>
            </div>
            
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
        await loadReviews(productId);
    } catch (err) {
        container.innerHTML = `<div class="col-12"><div class="alert alert-danger">Lỗi tải chi tiết: ${err.message}</div></div>`;
    }
}

async function addToCart(productId) {
    const qty = document.getElementById('qty').value;
    try {
        const res = await fetchAPI('/cart/add.php', {
            method: 'POST',
            body: { product_id: productId, quantity: qty }
        });
        showToast(res.message, 'success');
    } catch (err) {
        showToast(err.message, 'danger');
    }
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