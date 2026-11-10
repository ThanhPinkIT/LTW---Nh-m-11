<?php
include 'includes/header.php';
?>
<div class="container my-4">
    <div class="row g-3">
        <!-- Cột trái: Danh mục + Ưu đãi + Cam kết -->
<div class="col-lg-3 d-none d-lg-block">
    <div class="sidebar-wrapper">
        
<div class="category-panel">
            <div class="category-menu">
                
                <a href="javascript:void(0)" onclick="filterCategory('phone')" class="menu-item">
                    <div class="menu-item-left">
                        <div class="icon-box icon-phone">
                            <i class="bi bi-phone"></i>
                        </div>
                        <span class="menu-title">Điện thoại</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </a>

                <a href="javascript:void(0)" onclick="filterCategory('laptop')" class="menu-item">
                    <div class="menu-item-left">
                        <div class="icon-box icon-laptop">
                            <i class="bi bi-laptop"></i>
                        </div>
                        <span class="menu-title">Laptop & Tablet</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </a>

                <a href="javascript:void(0)" onclick="filterCategory('audio')" class="menu-item">
                    <div class="menu-item-left">
                        <div class="icon-box icon-audio">
                            <i class="bi bi-headphones"></i>
                        </div>
                        <span class="menu-title">Tai nghe & Loa</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </a>

                <a href="javascript:void(0)" onclick="filterCategory('watch')" class="menu-item">
                    <div class="menu-item-left">
                        <div class="icon-box icon-watch">
                            <i class="bi bi-smartwatch"></i>
                        </div>
                        <span class="menu-title">Smartwatch</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </a>

                <a href="javascript:void(0)" onclick="filterCategory('accessory')" class="menu-item">
                    <div class="menu-item-left">
                        <div class="icon-box icon-accessory">
                            <i class="bi bi-plug"></i>
                        </div>
                        <span class="menu-title">Phụ kiện cáp sạc</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </a>

            </div>
        </div>

        <!-- 2. Ưu đãi độc quyền (Voucher Card) -->
        <div class="exclusive-card">
            <span class="exclusive-tag">ƯU ĐÃI ĐỘC QUYỀN</span>
            <h3 class="exclusive-heading">Mã Giảm 50.000đ</h3>
            <p class="exclusive-subtext">Cho đơn hàng công nghệ đầu tiên trên 300k</p>
            
            <div class="coupon-box">
                <span class="coupon-text" id="voucherCodeText">NEWSHOP50K</span>
                <button type="button" class="btn-copy-coupon" onclick="copyVoucher()">Sao chép</button>
            </div>
        </div>

        <!-- 3. Yên tâm mua sắm -->
        <div class="trust-card">
            <div class="trust-header">
                <i class="bi bi-shield-check trust-shield-icon"></i>
                <span class="trust-header-title">YÊN TÂM MUA SẮM</span>
            </div>

            <div class="trust-list">
                <div class="trust-row">
                    <div class="trust-bubble bubble-blue">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <div class="trust-details">
                        <span class="trust-name">Giao hỏa tốc 2H</span>
                        <span class="trust-desc">Nội thành nhận ngay trong ngày</span>
                    </div>
                </div>

                <div class="trust-row">
                    <div class="trust-bubble bubble-green">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="trust-details">
                        <span class="trust-name">100% Chính hãng</span>
                        <span class="trust-desc">Cam kết hoàn tiền gấp 3 nếu fake</span>
                    </div>
                </div>

                <div class="trust-row">
                    <div class="trust-bubble bubble-purple">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="trust-details">
                        <span class="trust-name">Đổi trả 7 ngày</span>
                        <span class="trust-desc">Thủ tục siêu tốc 1 đổi 1</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="sidebar-ad" id="sidebarAdWrap" hidden>
        <div class="sidebar-ad-card" id="sidebarAd"></div>
    </div>
</div>

<!-- Cột phải (9 phần) -->
        <div class="col-lg-9">
            
            <div class="mobile-ad d-lg-none" id="mobileAd" hidden></div>

            <!-- 1. Banner Deal -->
            <div class="deal-banner-container">
                <div class="deal-banner-image-wrap">
                    <img src="uploads/banner.png" alt="Deal Đẹp Có Hạn" class="deal-banner-img">
                </div>
                <div class="deal-footer-bar">
                    <span class="deal-countdown-text">
                        Kết thúc: <span id="deal-timer">2:30:40</span>
                    </span>
                </div>
            </div>

            <!-- 2. 4 Ô phân loại dưới banner -->
<!-- 2. 4 Ô phân loại dưới banner -->
            <div class="deal-feature-grid">
                 <div class="deal-feature-card" onclick="filterTab('all')" style="cursor: pointer;">
                    <div class="deal-feature-inner"><span class="deal-feature-title">Tất cả</span></div>
                </div>
                <div class="deal-feature-card" onclick="filterTab('hot')" style="cursor: pointer;">
                    <div class="deal-feature-inner"><span class="deal-feature-title">Sản Phẩm Hot Trend</span></div>
                </div>
                <div class="deal-feature-card" onclick="filterTab('new')" style="cursor: pointer;">
                    <div class="deal-feature-inner"><span class="deal-feature-title">Hàng Mới Về</span></div>
                 </div>
                <div class="deal-feature-card" onclick="filterTab('sale')" style="cursor: pointer;">
                    <div class="deal-feature-inner"><span class="deal-feature-title">Giảm sâu</span></div>
                </div>
            </div>

            <!-- 3. LƯỚI SẢN PHẨM (CHỨA TOÀN BỘ CÁC THẺ CARD SẢN PHẨM CỦA BẠN) -->
 <!-- 1. LƯỚI SẢN PHẨM 3 CỘT (MỖI HÀNG 3 Ô, KHUNG NHỎ VỪA VẶN) -->
            <div class="product-store-grid" id="mainProductGrid">
                <div class="text-center py-5" style="grid-column: 1 / -1;"><div class="spinner-border text-danger" role="status"></div></div>
            </div>

            <!-- Thanh phân trang (chỉ 1 thanh, nằm NGOÀI lưới sản phẩm để luôn căn giữa) -->
            <div class="store-pagination">
                <button type="button" class="page-step-btn" id="prevBtn" onclick="changePage(currentPage - 1)">
                    &lsaquo;
                </button>
                <div class="page-numbers-list" id="pageNumbers"></div>
                <button type="button" class="page-step-btn" id="nextBtn" onclick="changePage(currentPage + 1)">
                    &rsaquo;
                </button>
            </div>

        </div> <!-- Đóng col-lg-9 -->
    </div> <!-- Đóng row g-3 -->
</div> <!-- Đóng container my-4 -->

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex d-none">
            <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm sản phẩm...">
            <button class="btn btn-primary" onclick="loadProducts()"> Tìm </button>
        </div>
    </div>
    
    <div class="row" id="productList">
        <!-- Products will be injected here via JS -->
        <div class="col-12 text-center">
        </div>
    </div>
</div>


<script>
    // 2 giờ 30 phút 40 giây = 9040 giây
    let remainTime = 2 * 3600 + 30 * 60 + 40;
    const timerDisplay = document.getElementById('deal-timer');

    setInterval(() => {
        if (remainTime <= 0) return;
        remainTime--;

        const hrs = Math.floor(remainTime / 3600);
        const mins = String(Math.floor((remainTime % 3600) / 60)).padStart(2, '0');
        const secs = String(remainTime % 60).padStart(2, '0');

        if (timerDisplay) {
            timerDisplay.textContent = `${hrs}:${mins}:${secs}`;
        }
    }, 1000);
// Format currency VND
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

async function loadProducts() {
    const list = document.getElementById('productList');
    const search = document.getElementById('searchInput').value.toLowerCase();
    
    list.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"></div></div>';
    
    try {
        const res = await fetchAPI('/product/index.php');
        const products = res.data;
        
        let filtered = products;
        if (search) {
            filtered = products.filter(p => p.name.toLowerCase().includes(search));
        }
        
        if (filtered.length === 0) {
            list.innerHTML = '<div class="col-12"><div class="alert alert-warning">Không tìm thấy sản phẩm.</div></div>';
            return;
        }
        
        // Show up to 8 products on homepage
        const displayProducts = filtered;
        
        let html = '';
        displayProducts.forEach(p => {
            // Fix image URL from API (which hardcoded localhost/shop/uploads)
            // Just extract the filename and point to uploads/
            const parts = p.image.split('/');
            const imgName = parts[parts.length - 1];
            
            html += `
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="uploads/${imgName}" class="card-img-top p-3" style="height:250px; object-fit:contain;" onerror="this.src='https://placehold.co/400x400/eeeeee/999999?text=No+Image';">
                    <div class="card-body text-center">
                        <h5 class="card-title">${p.name}</h5>
                        <p class="text-danger fw-bold">${formatVND(p.price)}</p>
                        <a href="product-detail.php?id=${p.id}" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>`;
        });
        list.innerHTML = html;
        
    } catch (err) {
        list.innerHTML = `<div class="col-12"><div class="alert alert-danger">Lỗi tải dữ liệu: ${err.message}</div></div>`;
    }
}
// Cấu hình đúng 6 sản phẩm mỗi trang
const PER_PAGE = 6;
let currentPage = 1;

function updatePagination() {
    const cards = document.querySelectorAll('.product-store-grid .product-store-card');
    const totalPages = Math.ceil(cards.length / PER_PAGE) || 1;
    const pageContainer = document.getElementById('pageNumbers');
    const prev = document.getElementById('prevBtn');
    const next = document.getElementById('nextBtn');

    if (!pageContainer) return;

    // Chỉ cho hiện đúng 6 sản phẩm của trang đang chọn
    cards.forEach((card, index) => {
        const start = (currentPage - 1) * PER_PAGE;
        const end = start + PER_PAGE;
        if (index >= start && index < end) {
            card.style.setProperty('display', 'flex', 'important');
        } else {
            card.style.setProperty('display', 'none', 'important');
        }
    });

    // Vẽ các nút 1, 2, 3...
    pageContainer.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `page-num-btn ${i === currentPage ? 'active' : ''}`;
        btn.innerText = i;
        btn.onclick = () => changePage(i);
        pageContainer.appendChild(btn);
    }

    if (prev) prev.disabled = (currentPage === 1);
    if (next) next.disabled = (currentPage === totalPages);
}

function changePage(page) {
    const cards = document.querySelectorAll('.product-store-grid .product-store-card');
    const totalPages = Math.ceil(cards.length / PER_PAGE) || 1;

    if (page < 1 || page > totalPages) return;
    currentPage = page;
    updatePagination();

    // Cuộn nhẹ lên đầu lưới sản phẩm khi bấm chuyển trang
    const grid = document.querySelector('.product-store-grid');
    if (grid) {
        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function filterCategory(type) {
    const keywords = {
        'phone': ['iphone', 'xiaomi', 'samsung', 'điện thoại'],
        'laptop': ['macbook', 'laptop', 'dell', 'asus', 'ipad'],
        'audio': ['airpods', 'tai nghe', 'loa', 'edifier', 'jbl'],
        'watch': ['watch', 'đồng hồ'],
        'accessory': ['sạc', 'cáp', 'chuột', 'bàn phím', 'pin dự phòng', 'cuktech']
    };

    const targetWords = keywords[type] || [];
    const cards = document.querySelectorAll('.product-store-card');

    cards.forEach(card => {
        const titleEl = card.querySelector('.product-card-title');
        const title = titleEl ? titleEl.innerText.toLowerCase() : '';

        // Kiểm tra xem tên sản phẩm có chứa từ khóa của danh mục không
        const isMatch = targetWords.some(word => title.includes(word));

        if (isMatch) {
            card.style.setProperty('display', 'flex', 'important');
        } else {
            card.style.setProperty('display', 'none', 'important');
        }
    });

    // Ẩn thanh phân trang tạm thời khi lọc để chỉ xem sản phẩm thuộc danh mục
    const pagination = document.querySelector('.store-pagination');
    if (pagination) {
        pagination.style.display = 'none';
    }
}

function filterTab(type) {
    const cards = Array.from(document.querySelectorAll('.product-store-card'));
    const pagination = document.querySelector('.store-pagination');

    if (type === 'all') {
        // Hiện lại toàn bộ và mở lại phân trang
        cards.forEach(card => card.style.setProperty('display', 'flex', 'important'));
        if (typeof updatePagination === 'function') {
            currentPage = 1;
            updatePagination();
        }
        if (pagination) pagination.style.display = 'flex';
        return;
    }

    // Tạm ẩn thanh phân trang khi xem theo danh mục lọc
    if (pagination) pagination.style.display = 'none';

    // Ẩn tất cả trước
    cards.forEach(card => card.style.setProperty('display', 'none', 'important'));

    let matched = [];

    if (type === 'hot') {
        // Lấy sản phẩm có nhãn HOT hoặc tên iPhone / AirPods
        matched = cards.filter(c => {
            const isHot = c.querySelector('.badge-hot') !== null;
            const title = c.querySelector('.product-card-title')?.innerText.toLowerCase() || '';
            return isHot || title.includes('iphone') || title.includes('airpods');
        });
    } else if (type === 'sale') {
        // Lấy sản phẩm có nhãn giảm giá (%)
        matched = cards.filter(c => c.querySelector('.badge-discount') !== null);
    } else if (type === 'new') {
        // Lấy 6 sản phẩm mới nhất (id lớn nhất)
        matched = cards.slice(-6);
    }

    // Nếu không khớp, lấy tạm 6 sản phẩm để không bị trống trang
    if (matched.length === 0) {
        matched = cards.slice(0, 6);
    }

    // Hiển thị sản phẩm phù hợp
    matched.forEach(card => card.style.setProperty('display', 'flex', 'important'));
}
// Khởi chạy khi tải trang
//document.addEventListener('DOMContentLoaded', loadProducts);
</script>

<script>
const escHtml = (t) => String(t ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const tenFileAnh = (path) => {
    let f = String(path || '').split(',')[0].trim().split('/').pop();
    try { f = decodeURIComponent(f); } catch (e) {}
    return f;
};
const ANH_TRONG = 'https://placehold.co/400x400/eeeeee/999999?text=No+Image';

function veTheSanPham(p) {
    const gia = Number(p.price) || 0;
    const giaGoc = Number(p.original_price) || 0;
    const giam = giaGoc > gia ? Math.round((1 - gia / giaGoc) * 100) : 0;
    const diem = Number(p.display_rating) || 0;
    const luot = Number(p.display_review_count) || 0;
    const hot = luot >= 100;
    const file = tenFileAnh(p.image);
    const anh = file ? 'uploads/' + encodeURIComponent(file) : ANH_TRONG;
    const link = 'product-detail.php?id=' + encodeURIComponent(p.id);

    return `
    <div class="product-store-card" data-product-id="${escHtml(p.id)}">
        <div class="product-thumb-wrap">
            ${giam > 0 ? `<span class="badge-discount">-${giam}%</span>` : ''}
            ${hot ? '<span class="badge-hot"><i class="bi bi-fire"></i> HOT</span>' : ''}
            <a href="${link}" class="product-link"><img src="${anh}" alt="${escHtml(p.name)}" class="product-thumb-img" onerror="this.onerror=null;this.src='${ANH_TRONG}';"></a>
        </div>
        <div class="product-card-body">
            ${diem > 0 ? `
            <div class="product-rating">
                <span class="rating-star">★</span>
                <span class="rating-score">${diem.toFixed(1)}</span>
                ${luot > 0 ? `<span class="rating-count">(${luot})</span>` : ''}
            </div>` : ''}
            <h4 class="product-card-title"><a href="${link}">${escHtml(p.name)}</a></h4>
            <div class="product-price-wrap">
                <span class="price-current">${gia.toLocaleString('vi-VN')} đ</span>
                ${giaGoc > gia ? `<span class="price-original">${giaGoc.toLocaleString('vi-VN')} đ</span>` : ''}
            </div>
            <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
        </div>
    </div>`;
}

let adTimer = null;
const AD_EXT = ['jpg', 'png', 'webp', 'jpeg'];

function adAnhLoi(img) {
    const next = Number(img.dataset.ext) + 1;
    if (next < AD_EXT.length) {
        img.dataset.ext = next;
        img.src = img.src.replace(/\.[a-z]+$/i, '.' + AD_EXT[next]);
        return;
    }
    img.onerror = function () { this.onerror = null; this.src = ANH_TRONG; };
    img.classList.add('ad-fallback');
    img.src = img.dataset.fallback;
}

function chonSanPhamQuangCao(list) {
    const uuTien = [/iphone/i, /macbook/i, /galaxy|samsung/i, /airpods pro/i, /ipad/i, /watch/i];
    const chon = [];
    uuTien.forEach(re => {
        const sp = list.find(p => re.test(p.name) && !chon.includes(p));
        if (sp) chon.push(sp);
    });
    list.slice().sort((a, b) => {
        const g = p => Number(p.original_price) > Number(p.price) ? 1 - Number(p.price) / Number(p.original_price) : 0;
        return g(b) - g(a);
    }).forEach(p => { if (chon.length < 4 && !chon.includes(p)) chon.push(p); });
    return chon.slice(0, 4);
}

function veQuangCao(list) {
    const wrap = document.getElementById('sidebarAdWrap');
    const box = document.getElementById('sidebarAd');
    if (!wrap || !box) return;
    const items = chonSanPhamQuangCao(list);
    if (!items.length) { wrap.hidden = true; return; }

    box.innerHTML = items.map((p, i) => {
        const gia = Number(p.price) || 0;
        const goc = Number(p.original_price) || 0;
        const giam = goc > gia ? Math.round((1 - gia / goc) * 100) : 0;
        const file = tenFileAnh(p.image);
        const anh = file ? 'uploads/' + encodeURIComponent(file) : ANH_TRONG;
        const link = 'product-detail.php?id=' + encodeURIComponent(p.id);
        return `
        <div class="ad-slide${i === 0 ? ' active' : ''}">
            <a class="ad-media" href="${link}">
                ${giam > 0 ? `<span class="ad-tag">Giảm ${giam}%</span>` : '<span class="ad-tag">Mới</span>'}
                <img src="uploads/ads/${encodeURIComponent(p.id)}.jpg" data-ext="0" data-fallback="${anh}" alt="${escHtml(p.name)}" crossorigin="anonymous" onerror="adAnhLoi(this)">
            </a>
            <div class="ad-info">
                <div class="ad-kicker">NexTech giới thiệu</div>
                <a class="ad-name" href="${link}">${escHtml(p.name)}</a>
                <div><span class="ad-price">${gia.toLocaleString('vi-VN')} đ</span>${goc > gia ? `<span class="ad-old">${goc.toLocaleString('vi-VN')} đ</span>` : ''}</div>
            </div>
            <a class="ad-btn" href="${link}">Mua ngay</a>
        </div>`;
    }).join('') + '<div class="ad-dots">' + items.map((_, i) => `<button type="button" aria-label="Quảng cáo ${i + 1}" class="${i === 0 ? 'active' : ''}"></button>`).join('') + '</div>';

    box.querySelectorAll('.ad-media img').forEach(img => {
        const tint = () => {
            try {
                const c = document.createElement('canvas');
                c.width = c.height = 4;
                const g = c.getContext('2d', { willReadFrequently: true });
                g.drawImage(img, 0, 0, 4, 4);
                const d = g.getImageData(0, 0, 1, 1).data;
                img.parentNode.style.background = `rgb(${d[0]},${d[1]},${d[2]})`;
            } catch (e) {}
        };
        if (img.complete) tint(); else img.addEventListener('load', tint);
    });

    const slides = box.querySelectorAll('.ad-slide');
    const dots = box.querySelectorAll('.ad-dots button');
    let cur = 0;
    const show = (n) => {
        cur = (n + slides.length) % slides.length;
        slides.forEach((s, i) => s.classList.toggle('active', i === cur));
        dots.forEach((d, i) => d.classList.toggle('active', i === cur));
    };
    dots.forEach((d, i) => d.addEventListener('click', () => show(i)));
    clearInterval(adTimer);
    if (slides.length > 1) {
        adTimer = setInterval(() => { if (!box.matches(':hover')) show(cur + 1); }, 5000);
    }
    wrap.hidden = false;
    veQuangCaoDiDong(items);
}

function veQuangCaoDiDong(items) {
    const m = document.getElementById('mobileAd');
    if (!m) return;
    m.innerHTML = items.map(p => {
        const gia = Number(p.price) || 0;
        const goc = Number(p.original_price) || 0;
        const giam = goc > gia ? Math.round((1 - gia / goc) * 100) : 0;
        const file = tenFileAnh(p.image);
        const anh = file ? 'uploads/' + encodeURIComponent(file) : ANH_TRONG;
        const link = 'product-detail.php?id=' + encodeURIComponent(p.id);
        return `
        <a class="mobile-ad-item" href="${link}">
            <span class="mobile-ad-img"><img src="${anh}" alt="${escHtml(p.name)}" loading="lazy" onerror="this.onerror=null;this.src='${ANH_TRONG}';"></span>
            <span class="mobile-ad-info">
                ${giam > 0 ? `<span class="mobile-ad-tag">Giảm ${giam}%</span>` : ''}
                <span class="mobile-ad-name">${escHtml(p.name)}</span>
                <span class="mobile-ad-price">${gia.toLocaleString('vi-VN')} đ</span>
            </span>
        </a>`;
    }).join('');
    m.hidden = false;
}

async function taiSanPhamTrangChu() {
    const grid = document.getElementById('mainProductGrid');
    try {
        const res = await fetchAPI('/product/index.php?_=' + Date.now());
        const list = (res.data || []).slice().sort((a, b) => a.id - b.id);

        if (!list.length) {
            grid.innerHTML = '<div class="alert alert-warning text-center" style="grid-column: 1 / -1;">Chưa có sản phẩm nào.</div>';
            return;
        }
        grid.innerHTML = list.map(veTheSanPham).join('');
        currentPage = 1;
        updatePagination();
        veQuangCao(list);
    } catch (e) {
        console.error('Không tải được danh sách sản phẩm', e);
        grid.innerHTML = '<div class="alert alert-danger text-center" style="grid-column: 1 / -1;">Lỗi tải sản phẩm: ' + escHtml(e.message) + '</div>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    taiSanPhamTrangChu();

    document.getElementById('mainProductGrid').addEventListener('click', (e) => {
        const button = e.target.closest('.btn-add-cart');
        if (!button) return;
        const card = button.closest('.product-store-card');
        const id = Number(card?.dataset.productId);
        if (!id) {
            thongBao('Sản phẩm này hiện chưa thể đặt mua', 'warning');
            return;
        }
        addToCart(id, button);
    });
});

window.addEventListener('pageshow', (e) => {
    if (e.persisted) taiSanPhamTrangChu();
});
</script>

<script src="assets/js/cart.js?v=6"></script>
<?php include 'includes/footer.php'; ?>