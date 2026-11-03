<?php
include 'includes/header.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2>Tất cả sản phẩm</h2>
        <div class="d-flex" style="max-width:400px;width:100%;">
            <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm tên sản phẩm...">
            <button class="btn btn-primary" onclick="loadProducts()"> Tìm </button>
        </div>
    </div>
    
    <!-- Notification -->
    <div id="toastMessage" class="alert d-none fixed-top mt-3 mx-auto shadow" style="max-width: 400px; z-index: 9999;"></div>

    <div class="row" id="productList">
        <!-- Products injected here -->
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
            list.innerHTML = '<div class="col-12"><div class="alert alert-warning">Không tìm thấy sản phẩm phù hợp.</div></div>';
            return;
        }
        
        let html = '';
        filtered.forEach(p => {
            const parts = p.image.split('/');
            const imgName = parts[parts.length - 1];
            
            html += `
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height:220px;padding:15px;">
                        <img src="uploads/${imgName}" class="img-fluid" alt="${p.name}" style="max-height:100%;object-fit:contain;" onerror="this.src='https://placehold.co/400x400/eeeeee/999999?text=No+Image';">
                    </div>
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title">${p.name}</h5>
                        <p class="text-danger fw-bold fs-5">${formatVND(p.price)}</p>
                        <div class="mt-auto"> 
                            <a href="product-detail.php?id=${p.id}" class="btn btn-outline-primary w-100 mb-2">Xem chi tiết</a>
                            <div class="input-group mb-2 mx-auto" style="max-width:130px;">
                                <span class="input-group-text">SL</span>
                                <input type="number" id="qty-${p.id}" class="form-control text-center" value="1" min="1">
                            </div>
                            <button onclick="addToCart(${p.id})" class="btn btn-success w-100">🛒 Thêm vào giỏ</button>
                        </div>
                    </div>
                </div>
            </div>`;
        });
        list.innerHTML = html;
        
    } catch (err) {
        list.innerHTML = `<div class="col-12"><div class="alert alert-danger">Lỗi tải dữ liệu: ${err.message}</div></div>`;
    }
}

async function addToCart(productId) {
    const qty = document.getElementById(`qty-${productId}`).value;
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

document.addEventListener('DOMContentLoaded', loadProducts);
</script>

<?php include 'includes/footer.php'; ?>