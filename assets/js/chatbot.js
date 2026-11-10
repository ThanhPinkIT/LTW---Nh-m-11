/**
 * CHATBOT.JS - Tech Assistant Widget (NexTech Store)
 * Rule-based Chatbot cho Website Bán Hàng Công Nghệ
 * Tối ưu hóa đa thiết bị: Desktop, Tablet & Mobile Phone
 */

(function () {
    'use strict';

    function initChatbot() {
        // 1. Khởi tạo DOM nếu chưa có
        initChatbotDOM();

        // 2. Lấy các phần tử DOM
        const launcher = document.getElementById('chatbotLauncher');
        const windowEl = document.getElementById('chatbotWindow');
        const closeBtn = document.getElementById('chatbotCloseBtn');
        const messagesEl = document.getElementById('chatbotMessages');
        const formEl = document.getElementById('chatbotForm');
        const inputEl = document.getElementById('chatbotInput');
        const sendBtn = document.getElementById('chatbotSendBtn');

        if (!launcher || !windowEl || !formEl || !inputEl) return;

        // Tránh gán sự kiện nhiều lần nếu hàm được gọi lại
        if (launcher.dataset.bound) return;
        launcher.dataset.bound = 'true';

        let isOpen = false;
        let isWaitingResponse = false;
        let hasShownWelcome = false;

        // 3. Hàm mở / đóng khung chat
        function toggleChat(forceOpen = null) {
            isOpen = forceOpen !== null ? forceOpen : !isOpen;
            if (isOpen) {
                windowEl.classList.add('is-open');
                launcher.classList.add('is-active');
                
                if (window.innerWidth > 600) {
                    inputEl.focus();
                }

                if (!hasShownWelcome) {
                    showWelcomeMessage();
                    hasShownWelcome = true;
                }
                scrollToBottom();
            } else {
                windowEl.classList.remove('is-open');
                launcher.classList.remove('is-active');
                if (window.innerWidth <= 600) {
                    windowEl.style.height = '';
                }
            }
        }

        launcher.addEventListener('click', () => toggleChat());
        if (closeBtn) {
            closeBtn.addEventListener('click', () => toggleChat(false));
        }

        // Tối ưu bàn phím ảo trên mobile
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                if (isOpen && window.innerWidth <= 600) {
                    windowEl.style.height = `${window.visualViewport.height}px`;
                    scrollToBottom();
                }
            });
        }

        // 4. Lời chào mừng ban đầu
        function showWelcomeMessage() {
            const welcomeText = "Xin chào! Mình là **Tech Assistant** - Trợ lý ảo của NexTech. Rất vui được hỗ trợ bạn!\n\nBạn có thể hỏi mình về tìm kiếm sản phẩm, tư vấn theo ngân sách, chính sách giao hàng, đổi trả hoặc mã giảm giá nhé!";
            const initialSuggestions = [
                "📱 Tìm điện thoại hot",
                "💻 Laptop dưới 25 triệu",
                "🚚 Chính sách giao hàng",
                "🏷️ Xem mã Voucher",
                "📍 Địa chỉ cửa hàng"
            ];
            appendBotMessage(welcomeText, [], initialSuggestions);
        }

        // 5. Xử lý gửi tin nhắn từ Form
        formEl.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = inputEl.value.trim();
            if (!text || isWaitingResponse) return;

            inputEl.value = '';
            handleUserMessage(text);
        });

        // 6. Gửi tin nhắn và gọi Backend API
        async function handleUserMessage(userText) {
            appendUserMessage(userText);
            scrollToBottom();

            removeSuggestions();
            showTypingIndicator();
            isWaitingResponse = true;
            if (sendBtn) sendBtn.disabled = true;

            try {
                const apiUrl = getChatbotApiUrl();

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: userText })
                });

                const data = await response.json();
                hideTypingIndicator();

                if (data && data.success && data.data) {
                    appendBotMessage(data.data.reply, data.data.products, data.data.suggestions);
                } else {
                    appendBotMessage(
                        data.message || "Rất tiếc, máy chủ không thể xử lý yêu cầu lúc này. Bạn vui lòng thử lại sau nhé!",
                        [],
                        ["📞 Liên hệ hotline", "🛒 Xem sản phẩm", "🏠 Về trang chủ"]
                    );
                }
            } catch (error) {
                console.error("Chatbot API Error:", error);
                hideTypingIndicator();
                appendBotMessage(
                    "Không thể kết nối đến máy chủ Tech Assistant. Bạn vui lòng kiểm tra kết nối mạng hoặc thử lại sau giây lát nhé!",
                    [],
                    ["🔄 Thử lại", "📞 Hotline: 0987.654.321"]
                );
            } finally {
                isWaitingResponse = false;
                if (sendBtn) sendBtn.disabled = false;
                scrollToBottom();
                if (window.innerWidth > 600) {
                    inputEl.focus();
                }
            }
        }

        // 7. Hàm xác định URL API linh hoạt
        function getChatbotApiUrl() {
            if (typeof API_BASE_URL !== 'undefined' && API_BASE_URL) {
                return `${API_BASE_URL}/chatbot/chat.php`;
            }

            const origin = window.location.origin;
            let path = window.location.pathname;

            path = path.replace(/\/[^\/]*\.[^\/]*$/, '');
            path = path.replace(/\/FRONTEND$/i, '');
            path = path.replace(/\/+$/, '');

            return `${origin}${path}/BACKEND/api/chatbot/chat.php`;
        }

        // 8. Chống XSS an toàn
        function escapeHTML(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // 9. Format Markdown
        function formatBotText(raw) {
            if (!raw) return '';
            let escaped = escapeHTML(raw);
            escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
            escaped = escaped.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
            escaped = escaped.replace(/\n/g, '<br>');
            return escaped;
        }

        // 10. Thêm tin nhắn của User
        function appendUserMessage(text) {
            const row = document.createElement('div');
            row.className = 'chatbot-msg-row is-user';
            row.innerHTML = `<div class="chatbot-bubble">${escapeHTML(text)}</div>`;
            messagesEl.appendChild(row);
        }

        // 11. Thêm tin nhắn của Bot
        function appendBotMessage(text, products = [], suggestions = []) {
            const row = document.createElement('div');
            row.className = 'chatbot-msg-row is-bot';

            let innerContent = `
                <div class="chatbot-msg-avatar" title="Tech Assistant">🤖</div>
                <div class="chatbot-msg-content" style="max-width: 100%;">
                    <div class="chatbot-bubble">${formatBotText(text)}</div>
            `;

            if (Array.isArray(products) && products.length > 0) {
                innerContent += `<div class="chatbot-products-wrap">`;
                products.forEach(p => {
                    const name = escapeHTML(p.name);
                    const price = escapeHTML(p.formatted_price || (p.price ? Number(p.price).toLocaleString('vi-VN') + ' đ' : ''));
                    const img = escapeHTML(p.imageUrl || 'uploads/banner.png');
                    const detailUrl = escapeHTML(p.detail_url || `product-detail.php?id=${p.id}`);

                    innerContent += `
                        <a href="${detailUrl}" class="chatbot-product-card" target="_self">
                            <img src="${img}" alt="${name}" class="chatbot-product-thumb" onerror="this.src='uploads/banner.png';">
                            <div class="chatbot-product-info">
                                <h4 class="chatbot-product-name" title="${name}">${name}</h4>
                                <p class="chatbot-product-price">${price}</p>
                            </div>
                            <span class="chatbot-product-btn">Xem ngay</span>
                        </a>
                    `;
                });
                innerContent += `</div>`;
            }

            innerContent += `</div></div>`;
            row.innerHTML = innerContent;
            messagesEl.appendChild(row);

            if (Array.isArray(suggestions) && suggestions.length > 0) {
                const sugWrap = document.createElement('div');
                sugWrap.className = 'chatbot-suggestions-wrap';
                suggestions.forEach(label => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'chatbot-chip';
                    btn.textContent = label;
                    btn.addEventListener('click', () => {
                        const cleanQuery = label.replace(/^[\p{Emoji}\u2000-\u3300\s]+/u, '').trim();
                        handleUserMessage(cleanQuery || label);
                    });
                    sugWrap.appendChild(btn);
                });
                messagesEl.appendChild(sugWrap);
            }

            scrollToBottom();
        }

        // 12. Typing Indicator
        function showTypingIndicator() {
            hideTypingIndicator();
            const typingEl = document.createElement('div');
            typingEl.id = 'chatbotTypingRow';
            typingEl.className = 'chatbot-msg-row is-bot';
            typingEl.innerHTML = `
                <div class="chatbot-msg-avatar">🤖</div>
                <div class="chatbot-typing" title="Đang trả lời...">
                    <span class="chatbot-typing-dot"></span>
                    <span class="chatbot-typing-dot"></span>
                    <span class="chatbot-typing-dot"></span>
                </div>
            `;
            messagesEl.appendChild(typingEl);
            scrollToBottom();
        }

        function hideTypingIndicator() {
            const typingEl = document.getElementById('chatbotTypingRow');
            if (typingEl) typingEl.remove();
        }

        function removeSuggestions() {
            const oldSug = messagesEl.querySelectorAll('.chatbot-suggestions-wrap');
            oldSug.forEach(el => el.remove());
        }

        function scrollToBottom() {
            setTimeout(() => {
                if (messagesEl) {
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }
            }, 50);
        }

        function initChatbotDOM() {
            if (document.getElementById('chatbotLauncher')) return;

            const widgetContainer = document.createElement('div');
            widgetContainer.id = 'chatbotWidgetContainer';
            widgetContainer.innerHTML = `
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
            `;
            document.body.appendChild(widgetContainer);
        }
    }

    // Khởi chạy an toàn ngay khi sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChatbot);
    } else {
        initChatbot();
    }
})();
