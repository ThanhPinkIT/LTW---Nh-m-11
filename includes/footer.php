<footer class="site-pink-footer">
    <div class="container py-4">
        <div class="row g-4">
            <!-- Cột 1: Địa chỉ -->
            <div class="col-md-3 col-sm-6">
                <h6 class="footer-col-title text-danger">
                    <i class="bi bi-geo-alt-fill me-1"></i> Địa chỉ
                </h6>
                <p class="footer-col-text">
                    Tầng 4, Tòa Nhà Công Nghệ, 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh
                </p>
                <small class="text-muted">Giờ hoạt động: 08:00 - 22:00 hàng ngày</small>
            </div>

            <!-- Cột 2: Số điện thoại -->
            <div class="col-md-3 col-sm-6">
                <h6 class="footer-col-title text-danger">
                    <i class="bi bi-telephone-fill me-1"></i> Số điện thoại
                </h6>
                <p class="footer-col-text mb-1">Tư vấn mua hàng: <b>0987.654.321</b></p>
                <p class="footer-col-text mb-1">Khiếu nại & Bảo hành: <b>1900.8899</b></p>
                <p class="footer-col-text">Email hỗ trợ: <b>support@shopcuatoi.vn</b></p>
            </div>

            <!-- Cột 3: Thông tin -->
            <div class="col-md-3 col-sm-6">
                <h6 class="footer-col-title text-danger">
                    <i class="bi bi-info-circle-fill me-1"></i> Thông tin
                </h6>
                <ul class="footer-links-list list-unstyled mb-0">
                    <li>Chính sách bảo hành 12 tháng</li>
                    <li>Quy chế đổi trả sản phẩm lỗi</li>
                    <li>Phương thức thanh toán & trả góp</li>
                    <li>Chính sách bảo mật người dùng</li>
                </ul>
            </div>

            <!-- Cột 4: Kết nối với shop -->
            <div class="col-md-3 col-sm-6">
                <h6 class="footer-col-title text-danger">
                    <i class="bi bi-share-fill me-1"></i> Kết nối với shop
                </h6>
                <p class="footer-col-text">Theo dõi để nhận quà tặng và video review công nghệ mới nhất:</p>
                <div class="footer-social-icons d-flex gap-2">
                    <a href="#" class="social-icon-circle icon-youtube"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="social-icon-circle icon-facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon-circle icon-tiktok"><i class="bi bi-tiktok"></i></a>
                    <a href="#" class="social-icon-circle icon-zalo">Zalo</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Chatbot Tech Assistant Widget -->
<link rel="stylesheet" href="assets/css/chatbot.css?v=20261110_99">

<button id="chatbotLauncher" class="chatbot-launcher" type="button" aria-label="Mở Tech Assistant Chatbot">
    <span class="chatbot-launcher-icon"><i class="bi bi-chat-dots-fill"></i></span>
    <span class="chatbot-launcher-badge" title="Đang hoạt động"></span>
</button>

<div id="chatbotWindow" class="chatbot-window" role="dialog" aria-modal="true" aria-label="Hộp thoại Tech Assistant">
    <div class="chatbot-header">
        <div class="chatbot-header-info">
            <div class="chatbot-avatar">🤖</div>
            <div>
                <h3 class="chatbot-title">Tech Assistant</h3>
                <div class="chatbot-status">
                    <span class="chatbot-status-dot"></span> Trực tuyến
                </div>
            </div>
        </div>
        <div class="chatbot-header-actions">
            <button id="chatbotCloseBtn" type="button" title="Đóng chat" aria-label="Đóng chat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div id="chatbotMessages" class="chatbot-messages"></div>
    <form id="chatbotForm" class="chatbot-input-area" autocomplete="off">
        <input 
            type="text" 
            id="chatbotInput" 
            class="chatbot-input" 
            placeholder="Hỏi Tech Assistant về sản phẩm, giá..." 
            maxlength="300" 
            required
        />
        <button id="chatbotSendBtn" class="chatbot-send-btn" type="submit" aria-label="Gửi tin nhắn">
            <i class="bi bi-send-fill"></i>
        </button>
    </form>
</div>

<script src="assets/js/chatbot.js?v=20261110_99" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
