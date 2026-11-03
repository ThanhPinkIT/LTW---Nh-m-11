<?php
include 'includes/header.php';
?>
<div class="container mt-4">
    <div class="text-white text-center p-5 rounded shadow" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('uploads/banner.jpg') center/cover no-repeat; min-height: 250px; display: flex; flex-direction: column; justify-content: center;">
        <h1 class="fw-bold"> Chào mừng đến với Shop Của Tôi </h1>
        <p class="fs-5"> Website bán hàng chuyên nghiệp </p>
    </div>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2> Sản phẩm nổi bật </h2>
        <div class="d-flex">
            <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm sản phẩm...">
            <button class="btn btn-primary" onclick="loadProducts()"> Tìm </button>
        </div>
    </div>
    
    <div class="row" id="productList">
        <!-- Products will be injected here via JS -->
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<script>
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

document.addEventListener('DOMContentLoaded', loadProducts);
</script>

<?php include 'includes/footer.php'; ?>