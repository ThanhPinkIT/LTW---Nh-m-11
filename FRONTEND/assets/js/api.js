// api.js - File tiện ích xử lý gọi API

// Trỏ đến BACKEND/api (FE ở /FRONTEND/, BE ở /BACKEND/api/)
const API_BASE_URL = (() => {
    const scriptEl = document.currentScript || document.querySelector('script[src*="api.js"]');
    const currentScriptSrc = scriptEl?.src || '';
    if (currentScriptSrc) {
        const url = new URL(currentScriptSrc, window.location.origin);
        return url.pathname.replace(/\/FRONTEND\/assets\/js\/api\.js(\?.*)?$/i, '/BACKEND/api');
    }
    return '/BACKEND/api';
})();

/**
 * Hàm gọi API chung
 * @param {string} endpoint - Đường dẫn sau /api (ví dụ: '/product/index.php')
 * @param {object} options - Cấu hình fetch (method, body...)
 */
async function fetchAPI(endpoint, options = {}) {
    const url = API_BASE_URL + endpoint;
    const defaultOptions = {
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
        }
    };

    if (options.method === 'POST' && options.body && !(options.body instanceof FormData)) {
        if (options.isJson) {
            defaultOptions.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(options.body);
        } else {
            const formData = new URLSearchParams();
            for (const key in options.body) {
                formData.append(key, options.body[key]);
            }
            options.body = formData;
            defaultOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
    }

    const finalOptions = { ...defaultOptions, ...options };

    try {
        const response = await fetch(url, finalOptions);
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error("Lỗi parse JSON:", text);
            throw new Error("Lỗi máy chủ không xác định");
        }
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Lỗi kết nối API');
        }
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}
