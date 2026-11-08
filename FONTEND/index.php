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
</div>

<!-- Cột phải (9 phần) -->
        <div class="col-lg-9">
            
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
                
                <!-- SP 1: iPhone 18 Pro Max -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-16%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/iphone18promax.jpg" alt="iPhone 18 Pro Max" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(248)</span>
                        </div>
                        <h4 class="product-card-title">iPhone 18 Pro Max 256GB Titan Tự Nhiên</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">29.490.000 đ</span>
                            <span class="price-original">34.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart"
                                onclick="addToCart(1)">
                            Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- SP 2: MacBook Air M2 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-14%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/macbookm3.jpg" alt="MacBook Air M2" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.8</span>
                            <span class="rating-count">(132)</span>
                        </div>
                        <h4 class="product-card-title">Laptop MacBook Air M2 13.6 inch (8GB/256GB)</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">23.990.000 đ</span>
                            <span class="price-original">27.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 3: Tai nghe Sony WH-1000XM5 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-20%</span>
                        <img src="uploads/Apple AirPods Max.jpg" alt="Tai nghe Sony" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">5.0</span>
                            <span class="rating-count">(89)</span>
                        </div>
                        <h4 class="product-card-title">Tai nghe Chống Ồn Không Dây Cao Cấp</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">6.790.000 đ</span>
                            <span class="price-original">8.490.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 4: Apple Watch 10 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-15%</span>
                        <img src="uploads/applewatch10.jpg" alt="Apple Watch Series 10" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.7</span>
                            <span class="rating-count">(110)</span>
                        </div>
                        <h4 class="product-card-title">Đồng hồ thông minh Apple Watch Series 10</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">9.490.000 đ</span>
                            <span class="price-original">11.290.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 5: AirPods Pro 2 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-12%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/airpodspro2.jpg" alt="AirPods Pro 2" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(312)</span>
                        </div>
                        <h4 class="product-card-title">Tai nghe Apple AirPods Pro 2 MagSafe USB-C</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">5.490.000 đ</span>
                            <span class="price-original">6.290.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 6: Cáp Cuktech -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-28%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/Cáp Cuktech Type-C to Lightning dài 1m AL870.jpg" alt="Cáp Cuktech" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.8</span>
                            <span class="rating-count">(420)</span>
                        </div>
                        <h4 class="product-card-title">Cáp sạc nhanh Type-C to Lightning AL870</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">180.000 đ</span>
                            <span class="price-original">250.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 7: Samsung S25 Ultra (Sang trang 2) -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-18%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/s25ultra.jpg" alt="Samsung S25 Ultra" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(190)</span>
                        </div>
                        <h4 class="product-card-title">Samsung Galaxy S25 Ultra 5G AI Phone</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">28.500.000 đ</span>
                            <span class="price-original">33.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 8: Dell UltraSharp 27 (Sang trang 2) -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-10%</span>
                        <img src="uploads/dellultrasharp.jpg" alt="Dell UltraSharp" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(142)</span>
                        </div>
                        <h4 class="product-card-title">Màn hình Dell UltraSharp 27 inch 4K</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">11.890.000 đ</span>
                            <span class="price-original">13.200.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 9: Chuột Logitech G Pro X (Sang trang 2) -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-22%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/logitechgprox.jpg" alt="Logitech G Pro X" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">5.0</span>
                            <span class="rating-count">(215)</span>
                        </div>
                        <h4 class="product-card-title">Chuột Không Dây Logitech G Pro X Superlight</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">2.390.000 đ</span>
                            <span class="price-original">3.090.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 10: Pin sạc dự phòng Ugreen (Sang trang 2) -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-20%</span>
                        <img src="uploads/Pin sạc dự phòng Magsafe Ugreen MagFlow 65958 10000mAh 25W.jpg" alt="Pin sạc dự phòng Ugreen" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(77)</span>
                        </div>
                        <h4 class="product-card-title">Pin sạc dự phòng MagSafe Ugreen 10000mAh</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">790.000 đ</span>
                            <span class="price-original">990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 11: iPad Air M3 (Sang trang 2) -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-13%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/ipadairm3.jpg" alt="iPad Air M3" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(112)</span>
                        </div>
                        <h4 class="product-card-title">Máy tính bảng Apple iPad Air Chip M3</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">15.990.000 đ</span>
                            <span class="price-original">18.400.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>

                <!-- SP 12: Loa JBL Charge 6 (Sang trang 2) -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-12%</span>
                        <span class="badge-hot">HOT</span>
                        <img src="uploads/Loa Bluetooth JBL Charge 6.jpg" alt="Loa JBL Charge 6" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="rating-score">4.7</span>
                            <span class="rating-count">(64)</span>
                        </div>
                        <h4 class="product-card-title">Loa Bluetooth JBL Charge 6 Chống Nước</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">3.490.000 đ</span>
                            <span class="price-original">3.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">Thêm vào giỏ</button>
                    </div>
                </div>
                <!-- 13. Apple AirPods Max Hồng -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-15%</span>
                        <img src="uploads/Apple AirPods Max hồng.jpg" alt="AirPods Max Hồng" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">5.0</span>
                            <span class="rating-count">(89)</span>
                        </div>
                        <h4 class="product-card-title">Tai nghe chụp tai Apple AirPods Max Hồng</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">12.490.000 đ</span>
                            <span class="price-original">13.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 14. Apple AirPods Max -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-12%</span>
                        <img src="uploads/Apple AirPods Max.jpg" alt="Apple AirPods Max" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(104)</span>
                        </div>
                        <h4 class="product-card-title">Tai nghe không dây Apple AirPods Max</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">12.290.000 đ</span>
                            <span class="price-original">13.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 15. Loa Bluetooth JBL Charge 6 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-12%</span>
                        <span class="badge-hot"><i class="bi bi-fire"></i> HOT</span>
                        <img src="uploads/Loa Bluetooth JBL Charge 6.jpg" alt="Loa JBL Charge 6" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.7</span>
                            <span class="rating-count">(64)</span>
                        </div>
                        <h4 class="product-card-title">Loa Bluetooth JBL Charge 6 Chống Nước IP67</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">3.490.000 đ</span>
                            <span class="price-original">3.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 16. Loa Bluetooth Edifier QD35 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-17%</span>
                        <img src="uploads/Loa Bluetooth Edifier QD35.jpg" alt="Edifier QD35" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.8</span>
                            <span class="rating-count">(45)</span>
                        </div>
                        <h4 class="product-card-title">Loa Bluetooth Edifier QD35 Đèn LED RGB</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">2.890.000 đ</span>
                            <span class="price-original">3.490.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 17. Loa KEF LSX II LT - White -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-8%</span>
                        <img src="uploads/Loa KEF LSX II LT - White.jpg" alt="KEF LSX II LT White" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">5.0</span>
                            <span class="rating-count">(32)</span>
                        </div>
                        <h4 class="product-card-title">Bộ Loa KEF LSX II LT - White Hi-Fi Cao Cấp</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">22.490.000 đ</span>
                            <span class="price-original">24.500.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 18. Apple Watch Series 10 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-15%</span>
                        <img src="uploads/applewatch10.jpg" alt="Apple Watch 10" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(95)</span>
                        </div>
                        <h4 class="product-card-title">Đồng hồ thông minh Apple Watch Series 10</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">10.790.000 đ</span>
                            <span class="price-original">11.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 19. REDMI Watch 6 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-25%</span>
                        <img src="uploads/REDMI Watch 6.jpg" alt="REDMI Watch 6" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.6</span>
                            <span class="rating-count">(81)</span>
                        </div>
                        <h4 class="product-card-title">Đồng hồ thông minh Xiaomi REDMI Watch 6</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">1.490.000 đ</span>
                            <span class="price-original">1.990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 20. Màn hình Dell UltraSharp -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-10%</span>
                        <img src="uploads/dellultrasharp.jpg" alt="Dell UltraSharp" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(142)</span>
                        </div>
                        <h4 class="product-card-title">Màn hình Dell UltraSharp 27 inch 4K Chuyên Đồ Họa</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">11.890.000 đ</span>
                            <span class="price-original">13.200.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 21. Màn hình LG UltraGear -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-16%</span>
                        <img src="uploads/lgultragear.jpg" alt="LG UltraGear" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.8</span>
                            <span class="rating-count">(99)</span>
                        </div>
                        <h4 class="product-card-title">Màn hình LG UltraGear Gaming 144Hz 1ms</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">5.490.000 đ</span>
                            <span class="price-original">6.500.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 22. Chuột Logitech G Pro X Superlight -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-22%</span>
                        <span class="badge-hot"><i class="bi bi-fire"></i> HOT</span>
                        <img src="uploads/logitechgprox.jpg" alt="Logitech G Pro X" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">5.0</span>
                            <span class="rating-count">(215)</span>
                        </div>
                        <h4 class="product-card-title">Chuột Không Dây Logitech G Pro X Superlight</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">2.390.000 đ</span>
                            <span class="price-original">3.090.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 23. Chuột Logitech G304 -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-30%</span>
                        <img src="uploads/g304.jpg" alt="Logitech G304" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.7</span>
                            <span class="rating-count">(340)</span>
                        </div>
                        <h4 class="product-card-title">Chuột Không Dây Logitech G304 Lightspeed</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">690.000 đ</span>
                            <span class="price-original">990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 24. Pin sạc dự phòng Ugreen MagFlow 10000mAh -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-20%</span>
                        <img src="uploads/Pin sạc dự phòng Magsafe Ugreen MagFlow 65958 10000mAh 25W.jpg" alt="Pin sạc dự phòng Ugreen" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(77)</span>
                        </div>
                        <h4 class="product-card-title">Pin sạc dự phòng MagSafe Ugreen MagFlow 10000mAh 25W</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">790.000 đ</span>
                            <span class="price-original">990.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <!-- 25. Cáp sạc Cuktech Type-C to Lightning -->
                <div class="product-store-card">
                    <div class="product-thumb-wrap">
                        <span class="badge-discount">-28%</span>
                        <span class="badge-hot"><i class="bi bi-fire"></i> HOT</span>
                        <img src="uploads/Cáp Cuktech Type-C to Lightning dài 1m AL870.jpg" alt="Cáp sạc Cuktech" class="product-thumb-img">
                    </div>
                    <div class="product-card-body">
                        <div class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(118)</span>
                        </div>
                        <h4 class="product-card-title">Cáp Cuktech Type-C to Lightning Dài 1m AL870</h4>
                        <div class="product-price-wrap">
                            <span class="price-current">180.000 đ</span>
                            <span class="price-original">250.000 đ</span>
                        </div>
                        <button type="button" class="btn-add-cart">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>

            </div> <!-- Đóng product-store-grid -->

            <!-- 2. THANH SỐ BẤM PHÂN TRANG (CÓ ĐỦ KHUNG SỐ Ở GIỮA) -->
            <<div class="store-pagination">
                <button type="button" class="page-step-btn" id="prevBtn" onclick="changePage(currentPage - 1)">
                </button>
                <div class="page-numbers-list" id="pageNumbers"></div>
                <button type="button" class="page-step-btn" id="nextBtn" onclick="changePage(currentPage + 1)">
                    >
                </button>
            </div>

        </div> <!-- Đóng col-lg-9 -->
    </div> <!-- Đóng row g-3 -->
</div> <!-- Đóng container my-4 -->
    </div>
</div>

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
        // Lấy 6 sản phẩm ở đầu
        matched = cards.slice(0, 6);
    }

    // Nếu không khớp, lấy tạm 6 sản phẩm để không bị trống trang
    if (matched.length === 0) {
        matched = cards.slice(0, 6);
    }

    // Hiển thị sản phẩm phù hợp
    matched.forEach(card => card.style.setProperty('display', 'flex', 'important'));
}
// Khởi chạy khi tải trang
document.addEventListener('DOMContentLoaded', updatePagination);
//document.addEventListener('DOMContentLoaded', loadProducts);
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.product-store-card')
        .forEach(card => {
            const button = card.querySelector('.btn-add-cart');

            if (!button) return;

            button.addEventListener('click', async () => {
                const productId = Number(card.dataset.productId);

                if (!Number.isSafeInteger(productId) || productId <= 0) {
                    alert('Sản phẩm chưa được liên kết với database.');
                    return;
                }

                if (typeof addToCart !== 'function') {
                    alert('Chức năng giỏ hàng chưa được tải.');
                    return;
                }

                await addToCart(productId);
            });
        });
});
</script>

<script src="assets/js/cart.js?v=<?= filemtime(__DIR__ . '/assets/js/cart.js') ?>"></script>
<?php include 'includes/footer.php'; ?>