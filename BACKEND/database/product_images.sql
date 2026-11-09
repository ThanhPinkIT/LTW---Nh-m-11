-- =====================================================
-- ẢNH PHỤ CHO SẢN PHẨM (để lướt xem bằng nút < > ở trang chi tiết)
-- Chạy 1 lần trong phpMyAdmin (chọn database web_ban_hang).
-- Ảnh chính vẫn nằm ở products.image, bảng này chỉ chứa ảnh thêm.
-- =====================================================

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ví dụ thêm ảnh phụ cho sản phẩm có ảnh chính là macbookm3.jpg
-- (file ảnh phải có sẵn trong thư mục uploads của FRONTEND):
-- INSERT INTO product_images (product_id, image, sort_order)
-- SELECT id, 'Apple MacBook Air 13 inch.jpg', 1 FROM products WHERE image = 'macbookm3.jpg';
