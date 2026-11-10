/**
 * CHATBOT.JS - Tech Assistant Widget (CellphoneS / CareSoft Style)
 * Rule-based Chatbot cho Website Bán Hàng Công Nghệ NexTech
 * Chuẩn Responsive Mobile & Desktop theo đúng chatbot.md
 */

(function () {
    'use strict';

    // SVG Mascot phong cách CellphoneS
    const MASCOT_SVG = `
        <svg viewBox="0 0 100 100" width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg">
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
    `;

    function initChatbot() {
        const launcher = document.getElementById('chatbotLauncher');
        const windowEl = document.getElementById('chatbotWindow');
        const closeBtn = document.getElementById('chatbotCloseBtn');
        const messagesEl = document.getElementById('chatbotMessages');
        const formEl = document.getElementById('chatbotForm');
        const inputEl = document.getElementById('chatbotInput');
        const sendBtn = document.getElementById('chatbotSendBtn');
        const emojiBtn = document.getElementById('chatbotEmojiBtn');

        if (!launcher || !windowEl || !formEl || !inputEl) return;

        if (launcher.dataset.bound) return;
        launcher.dataset.bound = 'true';

        let isOpen = false;
        let isWaitingResponse = false;
        let hasShownWelcome = false;

        // Mở / Đóng Chatbot
        function toggleChat(forceOpen = null) {
            isOpen = forceOpen !== null ? forceOpen : !isOpen;
            if (isOpen) {
                windowEl.classList.add('is-open');
                windowEl.style.display = 'flex';
                launcher.classList.add('is-active');

                if (window.innerWidth > 600) {
                    setTimeout(() => inputEl.focus(), 150);
                }

                if (!hasShownWelcome) {
                    showWelcomeMessage();
                    hasShownWelcome = true;
                }
                scrollToBottom();
            } else {
                windowEl.classList.remove('is-open');
                windowEl.style.display = 'none';
                launcher.classList.remove('is-active');
                if (window.innerWidth <= 600) {
                    windowEl.style.height = '';
                }
            }
        }

        window.toggleChatbot = toggleChat;
        window.__chatbotOnOpen = function() {
            if (!hasShownWelcome) {
                showWelcomeMessage();
                hasShownWelcome = true;
            }
            scrollToBottom();
        };

        launcher.addEventListener('click', (e) => {
            e.preventDefault();
            toggleChat();
        });
        launcher.addEventListener('touchend', (e) => {
            e.preventDefault();
            toggleChat();
        });
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                toggleChat(false);
            });
            closeBtn.addEventListener('touchend', (e) => {
                e.preventDefault();
                e.stopPropagation();
                toggleChat(false);
            });
        }

        // Bấm Emoji
        if (emojiBtn) {
            emojiBtn.addEventListener('click', () => {
                inputEl.value += ' 😊';
                inputEl.focus();
            });
        }

        // Bàn phím ảo mobile
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                if (isOpen && window.innerWidth <= 600) {
                    windowEl.style.height = `${window.visualViewport.height}px`;
                    scrollToBottom();
                }
            });
        }

        // Lời chào mừng ban đầu phong cách CellphoneS
        function showWelcomeMessage() {
            const welcomeText = "✨ **NEXTECH TECH STORE - CHÀO BẠN** 💥\n\nXin chào! Mình là **Tech Assistant** - Trợ lý ảo của NexTech.\nEm ở đây để hỗ trợ cho mình về tìm kiếm sản phẩm, tư vấn ngân sách, hướng dẫn mua hàng và mã giảm giá nhé!";
            const initialSuggestions = [
                "📱 Tìm điện thoại hot",
                "💻 Laptop dưới 25 triệu",
                "🛒 Hướng dẫn mua hàng",
                "🏷️ Xem mã Voucher",
                "📍 Địa chỉ cửa hàng"
            ];
            appendBotMessage(welcomeText, [], initialSuggestions);
        }

        // Gửi tin nhắn từ Form
        formEl.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = inputEl.value.trim();
            if (!text || isWaitingResponse) return;

            inputEl.value = '';
            handleUserMessage(text);
        });

        // Xử lý gửi tin nhắn
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

        function escapeHTML(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatBotText(raw) {
            if (!raw) return '';
            let escaped = escapeHTML(raw);
            escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
            escaped = escaped.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
            escaped = escaped.replace(/\n/g, '<br>');
            return escaped;
        }

        function appendUserMessage(text) {
            const row = document.createElement('div');
            row.className = 'chatbot-msg-row is-user';
            row.innerHTML = `
                <div class="chatbot-msg-body">
                    <div class="chatbot-bubble">${escapeHTML(text)}</div>
                    <div class="chatbot-timestamp">Bạn · Vừa xong</div>
                </div>
            `;
            messagesEl.appendChild(row);
        }

        function appendBotMessage(text, products = [], suggestions = []) {
            const row = document.createElement('div');
            row.className = 'chatbot-msg-row is-bot';

            let innerContent = `
                <div class="chatbot-msg-avatar">${MASCOT_SVG}</div>
                <div class="chatbot-msg-body">
                    <div class="chatbot-bubble">${formatBotText(text)}</div>
                    <div class="chatbot-timestamp">TECH ASSISTANT · Vừa xong</div>
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

            innerContent += `</div>`;
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

        function showTypingIndicator() {
            hideTypingIndicator();
            const typingEl = document.createElement('div');
            typingEl.id = 'chatbotTypingRow';
            typingEl.className = 'chatbot-msg-row is-bot';
            typingEl.innerHTML = `
                <div class="chatbot-msg-avatar">${MASCOT_SVG}</div>
                <div class="chatbot-msg-body">
                    <div class="chatbot-typing" title="Đang trả lời...">
                        <span class="chatbot-typing-dot"></span>
                        <span class="chatbot-typing-dot"></span>
                        <span class="chatbot-typing-dot"></span>
                    </div>
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
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChatbot);
    } else {
        initChatbot();
    }
})();
