-- =========================================
-- THÔNG TIN GIAO HÀNG CỦA ĐƠN HÀNG
-- Import vào database web_ban_hang (chạy SAU shop.sql và voucher.sql)
-- Lưu ý: code PHP cũng tự thêm các cột này nếu thiếu (helper/order.php),
-- file này để bạn chủ động chạy trong phpMyAdmin nếu muốn.
-- =========================================

ALTER TABLE orders
    ADD COLUMN receiver_name    VARCHAR(100) NOT NULL DEFAULT ''  AFTER user_id,
    ADD COLUMN receiver_phone   VARCHAR(20)  NOT NULL DEFAULT ''  AFTER receiver_name,
    ADD COLUMN shipping_city    VARCHAR(100) NOT NULL DEFAULT ''  AFTER receiver_phone,
    ADD COLUMN shipping_ward    VARCHAR(100) NOT NULL DEFAULT ''  AFTER shipping_city,
    ADD COLUMN shipping_address VARCHAR(255) NOT NULL DEFAULT ''  AFTER shipping_ward,
    ADD COLUMN note             VARCHAR(500) DEFAULT NULL         AFTER shipping_address;
