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

<!-- ==========================================
     CHATBOT TECH ASSISTANT (CELLPHONES STYLE)
     ========================================== -->
<style>
/* CSS Cố định Chatbot Widget - Max Z-Index đảm bảo luôn bấm được */
#chatbotLauncher {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    width: 60px !important;
    height: 60px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #d70018 0%, #b80014 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 6px 20px rgba(215, 0, 24, 0.45) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    z-index: 2147483647 !important;
    border: 3px solid #ffffff !important;
    padding: 0 !important;
    outline: none !important;
    pointer-events: auto !important;
    touch-action: manipulation !important;
    -webkit-tap-highlight-color: transparent !important;
    transform: translateZ(0) !important;
    will-change: transform !important;
    user-select: none !important;
}
#chatbotLauncher * {
    pointer-events: none !important;
}
#chatbotWindow {
    position: fixed !important;
    bottom: 96px !important;
    right: 24px !important;
    width: 370px !important;
    max-width: calc(100vw - 32px) !important;
    height: 540px !important;
    max-height: calc(100vh - 120px) !important;
    background: #ffffff !important;
    border-radius: 18px !important;
    box-shadow: 0 12px 36px rgba(0,0,0,0.2) !important;
    display: none;
    flex-direction: column !important;
    overflow: hidden !important;
    z-index: 2147483646 !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    pointer-events: auto !important;
}
#chatbotWindow.is-open {
    display: flex !important;
}
@media (max-width: 600px) {
    #chatbotLauncher {
        bottom: max(20px, env(safe-area-inset-bottom)) !important;
        right: 18px !important;
        width: 56px !important;
        height: 56px !important;
    }
    #chatbotWindow {
        top: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        border-radius: 0 !important;
        margin: 0 !important;
        border: none !important;
        z-index: 2147483647 !important;
    }
}
</style>

<link rel="stylesheet" href="assets/css/chatbot.css?v=20261110_400">

<!-- Nút Chatbot nổi (CareSoft / CellphoneS Mascot) -->
<button id="chatbotLauncher" class="chatbot-launcher" type="button" aria-label="Mở Tech Assistant Chatbot" onclick="toggleChatbot(true)">
    <div class="chatbot-launcher-inner">
        <svg viewBox="0 0 100 100" width="38" height="38" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="46" fill="#ffffff" stroke="#d70018" stroke-width="4"/>
            <path d="M18 50 C18 28 82 28 82 50" stroke="#d70018" stroke-width="5" stroke-linecap="round"/>
            <rect x="12" y="44" width="10" height="18" rx="5" fill="#d70018"/>
            <rect x="78" y="44" width="10" height="18" rx="5" fill="#d70018"/>
            <path d="M80 58 C80 72 65 76 56 76" stroke="#d70018" stroke-width="3" stroke-linecap="round"/>
            <circle cx="54" cy="76" r="4" fill="#d70018"/>
            <ellipse cx="36" cy="48" rx="5" ry="7" fill="#222"/>
            <circle cx="38" cy="46" r="2" fill="#fff"/>
            <ellipse cx="64" cy="48" rx="5" ry="7" fill="#222"/>
            <circle cx="66" cy="46" r="2" fill="#fff"/>
            <ellipse cx="28" cy="56" rx="6" ry="3.5" fill="#ff8a80"/>
            <ellipse cx="72" cy="56" rx="6" ry="3.5" fill="#ff8a80"/>
            <path d="M42 56 Q50 64 58 56" stroke="#d70018" stroke-width="3.5" stroke-linecap="round" fill="none"/>
        </svg>
    </div>
    <span class="chatbot-launcher-badge" title="Đang hoạt động"></span>
</button>

<!-- Khung cửa sổ Chat CellphoneS Style -->
<div id="chatbotWindow" class="chatbot-window" role="dialog" aria-modal="true" aria-label="Hộp thoại Tech Assistant">
    <!-- Header màu đỏ CellphoneS -->
    <div class="chatbot-header">
        <div class="chatbot-header-left">
            <div class="chatbot-header-avatar">
                <svg viewBox="0 0 100 100" width="38" height="38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="46" fill="#ffffff" stroke="#d70018" stroke-width="4"/>
                    <path d="M18 50 C18 28 82 28 82 50" stroke="#d70018" stroke-width="5" stroke-linecap="round"/>
                    <rect x="12" y="44" width="10" height="18" rx="5" fill="#d70018"/>
                    <rect x="78" y="44" width="10" height="18" rx="5" fill="#d70018"/>
                    <path d="M80 58 C80 72 65 76 56 76" stroke="#d70018" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="54" cy="76" r="4" fill="#d70018"/>
                    <ellipse cx="36" cy="48" rx="5" ry="7" fill="#222"/>
                    <circle cx="38" cy="46" r="2" fill="#fff"/>
                    <ellipse cx="64" cy="48" rx="5" ry="7" fill="#222"/>
                    <circle cx="66" cy="46" r="2" fill="#fff"/>
                    <ellipse cx="28" cy="56" rx="6" ry="3.5" fill="#ff8a80"/>
                    <ellipse cx="72" cy="56" rx="6" ry="3.5" fill="#ff8a80"/>
                    <path d="M42 56 Q50 64 58 56" stroke="#d70018" stroke-width="3.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
            <div class="chatbot-header-text">
                <h3 class="chatbot-header-title">Xin chào!</h3>
                <p class="chatbot-header-subtitle">Em ở đây để hỗ trợ cho mình...</p>
            </div>
        </div>
        <div class="chatbot-header-actions">
            <button type="button" title="Menu" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
            <button id="chatbotCloseBtn" type="button" title="Đóng chat" aria-label="Đóng chat" onclick="toggleChatbot(false)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <!-- Danh sách tin nhắn -->
    <div id="chatbotMessages" class="chatbot-messages"></div>

    <!-- Ô nhập tin nhắn CellphoneS Style -->
    <form id="chatbotForm" class="chatbot-input-area" autocomplete="off">
        <div class="chatbot-input-box">
            <input 
                type="text" 
                id="chatbotInput" 
                class="chatbot-input" 
                placeholder="Nhập nội dung..." 
                maxlength="300" 
                required
            />
            <div class="chatbot-input-icons">
                <button type="button" id="chatbotEmojiBtn" class="chatbot-emoji-btn" title="Chèn emoji">
                    <i class="bi bi-emoji-smile"></i>
                </button>
                <button id="chatbotSendBtn" class="chatbot-send-btn" type="submit" aria-label="Gửi tin nhắn">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
        <div class="chatbot-footer-brand">
            <span class="brand-caresoft"><b style="color: #2e7d32; font-weight: 700;">Care</b><span style="color: #888888; font-weight: 500;">Soft</span></span>
        </div>
    </form>
</div>

<!-- Script Inline đảm bảo BẤM LÀ MỞ NGAY LẬP TỨC kể cả khi file ngoài bị cache -->
<script>
function toggleChatbot(forceState) {
    var win = document.getElementById('chatbotWindow');
    var btn = document.getElementById('chatbotLauncher');
    if (!win) return;
    var openNow = (forceState !== undefined && forceState !== null) 
        ? forceState 
        : !win.classList.contains('is-open');

    if (openNow) {
        win.classList.add('is-open');
        win.style.display = 'flex';
        if (btn) btn.classList.add('is-active');
        if (window.innerWidth > 600) {
            setTimeout(function() {
                var inp = document.getElementById('chatbotInput');
                if (inp) inp.focus();
            }, 120);
        }
        if (window.__chatbotOnOpen) {
            window.__chatbotOnOpen();
        }
    } else {
        win.classList.remove('is-open');
        win.style.display = 'none';
        if (btn) btn.classList.remove('is-active');
        if (window.innerWidth <= 600) {
            win.style.height = '';
        }
    }
}
window.toggleChatbot = toggleChatbot;
</script>

<script src="assets/js/chatbot.js?v=20261110_400"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
