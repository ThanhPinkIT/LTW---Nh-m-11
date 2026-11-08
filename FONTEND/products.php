<?php
include 'includes/header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2>Tất cả sản phẩm</h2>
        <form class="d-none" id="searchForm">
            <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm tên sản phẩm...">
            <button class="btn btn-primary" onclick="loadProducts()"> Tìm </button>
        </form>
    </div>

    
    <!-- Notification -->
    <div id="toastMessage" class="alert d-none fixed-top mt-3 mx-auto shadow" style="max-width: 400px; z-index: 9999;"></div>

<!-- KHUNG LƯỚI SẢN PHẨM 3 CỘT THEO MẪU -->
    <div class="product-store-grid" id="productList"></div>

    <!-- THANH PHÂN TRANG -->
    <div class="store-pagination mt-4 mb-4" id="paginationWrap" style="display: none;">
        <button type="button" class="page-step-btn" id="prevBtn">&lsaquo;</button>
        <div class="page-numbers-list" id="pageNumbers"></div>
        <button type="button" class="page-step-btn" id="nextBtn">&rsaquo;</button>
    </div>
</div>

<script>
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toastMessage');
    toast.className = `alert alert-${type} fixed-top mt-3 mx-auto shadow`;
    toast.textContent = msg;
    toast.classList.remove('d-none');

    setTimeout(() => {
        toast.classList.add('d-none');
    }, 3000);
}

const PER_PAGE = 6;
let currentPage = 1;
let currentProducts = [];

async function loadProducts() {
    const list = document.getElementById('productList');   
    const urlParams = new URLSearchParams(window.location.search);
    const search = (
        urlParams.get('search') ||
        document.getElementById('searchInput')?.value ||
        ''
        ).trim().toLowerCase();

    
    list.innerHTML = '<div class="text-center w-100 py-4"><div class="spinner-border text-danger" role="status"></div></div>';

    try {
        const res = await fetchAPI('/product/index.php');
        const products = res.data || [];

        let filtered = products;
        if (search) {
            filtered = products.filter(p => p.name.toLowerCase().includes(search));
        }

        if (filtered.length === 0) {
            list.innerHTML = '<div class="alert alert-warning text-center w-100">Không tìm thấy sản phẩm phù hợp.</div>';
            document.getElementById('paginationWrap').style.display = 'none';
            return;
        }

        currentProducts = filtered;
        currentPage = 1;
        renderPage();

    } catch (e) {
        list.innerHTML = '<div class="alert alert-danger text-center w-100">Lỗi khi tải danh sách sản phẩm!</div>';
        console.error(e);
    }
}

function renderPage() {
    const list = document.getElementById('productList');
    const paginationWrap = document.getElementById('paginationWrap');
    const pageContainer = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    const totalPages = Math.ceil(currentProducts.length / PER_PAGE) || 1;
    const start = (currentPage - 1) * PER_PAGE;
    const end = start + PER_PAGE;
    const pageItems = currentProducts.slice(start, end);

    let html = '';
    pageItems.forEach(p => {
        // Tách ảnh đầu tiên
        let img = p.image ? p.image.split(',')[0].trim() : 'banner.png';
        if (!img.startsWith('http') && !img.startsWith('uploads/')) {
            img = 'uploads/' + img;
        }

        html += `
        <div class="product-store-card">
            <div class="product-thumb-wrap">
                <span class="badge-discount">-15%</span>
                <span class="badge-hot"><i class="bi bi-fire"></i> HOT</span>
                <img src="${img}" alt="${p.name}" class="product-thumb-img">
            </div>
            <div class="product-card-body">
                <div class="product-rating">
                    <i class="bi bi-star-fill text-warning"></i>
                    <span class="rating-score">4.9</span>
                    <span class="rating-count">(312)</span>
                </div>
                <h4 class="product-card-title">${p.name}</h4>
                <div class="product-price-wrap">
                    <span class="price-current">${formatVND(p.price)}</span>
                    <span class="price-original">${formatVND(Number(p.price) * 1.15)}</span>
                </div>
                <button type="button" class="btn-add-cart" onclick="addToCart(${p.id})">
                    Thêm vào giỏ
                </button>
            </div>
        </div>
        `;
    });

    list.innerHTML = html;

    // Hiển thị phân trang
    if (totalPages > 1) {
        paginationWrap.style.display = 'flex';
        pageContainer.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `page-num-btn ${i === currentPage ? 'active' : ''}`;
            btn.innerText = i;
            btn.onclick = () => {
                currentPage = i;
                renderPage();
                window.scrollTo({ top: 100, behavior: 'smooth' });
            };
            pageContainer.appendChild(btn);
        }

        prevBtn.disabled = (currentPage === 1);
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage();
                window.scrollTo({ top: 100, behavior: 'smooth' });
            }
        };

        nextBtn.disabled = (currentPage === totalPages);
        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage();
                window.scrollTo({ top: 100, behavior: 'smooth' });
            }
        };
    } else {
        paginationWrap.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', loadProducts);
</script>

<script src="assets/js/cart.js?v=<?= filemtime(__DIR__ . '/assets/js/cart.js') ?>"></script>
<?php include 'includes/footer.php'; ?>