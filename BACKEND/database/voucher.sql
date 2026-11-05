-- =========================================
-- VOUCHER FEATURE (chạy SAU shop.sql)
-- Import vào database web_ban_hang
-- =========================================

CREATE TABLE IF NOT EXISTS vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    -- percent: giảm theo %, fixed: giảm số tiền cố định, freeship: giảm phí vận chuyển
    type ENUM('percent','fixed','freeship') NOT NULL,
    value DECIMAL(12,2) NOT NULL DEFAULT 0,
    max_discount DECIMAL(12,2) DEFAULT NULL,   -- trần giảm (NULL = không giới hạn)
    min_order DECIMAL(12,2) NOT NULL DEFAULT 0, -- giá trị đơn tối thiểu
    usage_limit INT DEFAULT NULL,              -- tổng lượt dùng (NULL = không giới hạn)
    used_count INT NOT NULL DEFAULT 0,
    per_user_limit INT NOT NULL DEFAULT 1,     -- mỗi user dùng tối đa mấy lần (0 = không giới hạn)
    start_date DATETIME DEFAULT NULL,
    end_date DATETIME DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS voucher_usages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    INDEX idx_voucher_user (voucher_id, user_id)
) ENGINE=InnoDB;

-- Lưu thông tin giảm giá / phí ship vào đơn hàng
-- (total_money = tạm tính + shipping_fee - discount_amount)
ALTER TABLE orders
    ADD COLUMN shipping_fee DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER total_money,
    ADD COLUMN discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER shipping_fee,
    ADD COLUMN voucher_code VARCHAR(50) DEFAULT NULL AFTER discount_amount;

-- Dữ liệu mẫu (khớp với 3 mã đang hiển thị ở FE)
INSERT INTO vouchers
(code, description, type, value, max_discount, min_order, usage_limit, per_user_limit, start_date, end_date)
VALUES
('GIAM20',   'Giảm 20% tối đa 100.000đ cho mọi đơn hàng', 'percent',  20,    100000, 0,      NULL, 0, NULL, NULL),
('FREESHIP', 'Miễn phí giao hàng (giảm tối đa 30.000đ)',  'freeship', 0,     30000,  0,      NULL, 0, NULL, NULL),
('TECH50K',  'Giảm trực tiếp 50.000đ vào đơn hàng',       'fixed',    50000, NULL,   200000, 100,  1, NULL, NULL);
