CREATE DATABASE IF NOT EXISTS web_ban_hang
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE web_ban_hang;

-- Drop existing tables to prevent "Table already exists" error
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- =========================================
-- USERS
-- =========================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin','customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================
-- CATEGORIES
-- =========================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- =========================================
-- PRODUCTS
-- =========================================


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(12,2) NOT NULL,
    original_price DECIMAL(12,2) DEFAULT NULL,

    -- Danh gia hien thi theo FE
    display_rating DECIMAL(2,1) DEFAULT 0.0,
    display_review_count INT DEFAULT 0,

    quantity INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- =========================================
-- ORDERS
-- =========================================

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_money DECIMAL(12,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'COD',
    status ENUM(
        'Pending',
        'Processing',
        'Shipping',
        'Completed',
        'Cancelled'
    ) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- ORDER DETAILS
-- =========================================

CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,

    FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- PRODUCT REVIEWS
-- =========================================

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reviews_user
        FOREIGN KEY (user_id)
        REFERENCES users(id),

    CONSTRAINT fk_reviews_product
        FOREIGN KEY (product_id)
        REFERENCES products(id),

    CONSTRAINT chk_reviews_rating
        CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- =========================================
-- SAMPLE CATEGORY DATA
-- =========================================

INSERT INTO categories (id, name)
VALUES
(1, 'Laptop'),
(2, 'Điện thoại'),
(3, 'Phụ kiện'),
(4, 'Gaming'),
(5, 'Màn hình'),
(6, 'Máy tính bảng');

-- =========================================
-- ADMIN ACCOUNT
-- Username: admin
-- Password: password
-- =========================================

INSERT INTO users
(
username,
password,
fullname,
email,
role
)
VALUES
(
'admin',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
'Administrator',
'admin@gmail.com',
'admin'
);



-- =========================================
-- SAMPLE PRODUCTS 
-- =========================================

INSERT INTO products
(id, category_id, name, description,
 price, original_price, quantity, image)
VALUES

(1, 2, 'iPhone 18 Pro Max 256GB Titan Tự Nhiên',
'iPhone 18 Pro Max 256GB Titan Tự Nhiên',
29490000, 34990000, 10, 'iphone18promax.jpg'),

(2, 1, 'Laptop MacBook Air M2 13.6 inch (8GB/256GB)',
'Laptop MacBook Air M2 13.6 inch (8GB/256GB)',
23990000, 27990000, 10, 'macbookm3.jpg'),

(3, 3, 'Tai nghe Chống Ồn Không Dây Cao Cấp',
'Tai nghe Chống Ồn Không Dây Cao Cấp',
6790000, 8490000, 10, 'Apple AirPods Max.jpg'),

(4, 3, 'Đồng hồ thông minh Apple Watch Series 10',
'Đồng hồ thông minh Apple Watch Series 10',
9490000, 11290000, 10, 'applewatch10.jpg'),

(5, 3, 'Tai nghe Apple AirPods Pro 2 MagSafe USB-C',
'Tai nghe Apple AirPods Pro 2 MagSafe USB-C',
5490000, 6290000, 10, 'airpodspro2.jpg'),

(6, 3, 'Cáp sạc nhanh Type-C to Lightning AL870',
'Cáp sạc nhanh Type-C to Lightning AL870',
180000, 250000, 10,
'Cáp Cuktech Type-C to Lightning dài 1m AL870.jpg'),

(7, 2, 'Samsung Galaxy S25 Ultra 5G AI Phone',
'Samsung Galaxy S25 Ultra 5G AI Phone',
28500000, 33990000, 10, 's25ultra.jpg'),

(8, 5, 'Màn hình Dell UltraSharp 27 inch 4K',
'Màn hình Dell UltraSharp 27 inch 4K',
11890000, 13200000, 10, 'dellultrasharp.jpg'),

(9, 3, 'Chuột Không Dây Logitech G Pro X Superlight',
'Chuột Không Dây Logitech G Pro X Superlight',
2390000, 3090000, 10, 'logitechgprox.jpg'),

(10, 3, 'Pin sạc dự phòng MagSafe Ugreen 10000mAh',
'Pin sạc dự phòng MagSafe Ugreen 10000mAh',
790000, 990000, 10,
'Pin sạc dự phòng Magsafe Ugreen MagFlow 65958 10000mAh 25W.jpg'),

(11, 6, 'Máy tính bảng Apple iPad Air Chip M3',
'Máy tính bảng Apple iPad Air Chip M3',
15990000, 18400000, 10, 'ipadairm3.jpg'),

(12, 3, 'Loa Bluetooth JBL Charge 6 Chống Nước',
'Loa Bluetooth JBL Charge 6 Chống Nước',
3490000, 3990000, 10, 'Loa Bluetooth JBL Charge 6.jpg'),

(13, 3, 'Tai nghe chụp tai Apple AirPods Max Hồng',
'Tai nghe chụp tai Apple AirPods Max Hồng',
12490000, 13990000, 10, 'Apple AirPods Max hồng.jpg'),

(14, 3, 'Tai nghe không dây Apple AirPods Max',
'Tai nghe không dây Apple AirPods Max',
12290000, 13990000, 10, 'Apple AirPods Max.jpg'),

(15, 3, 'Loa Bluetooth JBL Charge 6 Chống Nước IP67',
'Loa Bluetooth JBL Charge 6 Chống Nước IP67',
3490000, 3990000, 10, 'Loa Bluetooth JBL Charge 6.jpg'),

(16, 3, 'Loa Bluetooth Edifier QD35 Đèn LED RGB',
'Loa Bluetooth Edifier QD35 Đèn LED RGB',
2890000, 3490000, 10, 'Loa Bluetooth Edifier QD35.jpg'),

(17, 3, 'Bộ Loa KEF LSX II LT - White Hi-Fi Cao Cấp',
'Bộ Loa KEF LSX II LT - White Hi-Fi Cao Cấp',
22490000, 24500000, 10, 'Loa KEF LSX II LT - White.jpg'),

(18, 3, 'Đồng hồ thông minh Apple Watch Series 10',
'Đồng hồ thông minh Apple Watch Series 10',
10790000, 11990000, 10, 'applewatch10.jpg'),

(19, 3, 'Đồng hồ thông minh Xiaomi REDMI Watch 6',
'Đồng hồ thông minh Xiaomi REDMI Watch 6',
1490000, 1990000, 10, 'REDMI Watch 6.jpg'),

(20, 5, 'Màn hình Dell UltraSharp 27 inch 4K Chuyên Đồ Họa',
'Màn hình Dell UltraSharp 27 inch 4K Chuyên Đồ Họa',
11890000, 13200000, 10, 'dellultrasharp.jpg'),

(21, 5, 'Màn hình LG UltraGear Gaming 144Hz 1ms',
'Màn hình LG UltraGear Gaming 144Hz 1ms',
5490000, 6500000, 10, 'lgultragear.jpg'),

(22, 3, 'Chuột Không Dây Logitech G Pro X Superlight',
'Chuột Không Dây Logitech G Pro X Superlight',
2390000, 3090000, 10, 'logitechgprox.jpg'),

(23, 3, 'Chuột Không Dây Logitech G304 Lightspeed',
'Chuột Không Dây Logitech G304 Lightspeed',
690000, 990000, 10, 'g304.jpg'),

(24, 3, 'Pin sạc dự phòng MagSafe Ugreen MagFlow 10000mAh 25W',
'Pin sạc dự phòng MagSafe Ugreen MagFlow 10000mAh 25W',
790000, 990000, 10,
'Pin sạc dự phòng Magsafe Ugreen MagFlow 65958 10000mAh 25W.jpg'),

(25, 3, 'Cáp Cuktech Type-C to Lightning Dài 1m AL870',
'Cáp Cuktech Type-C to Lightning Dài 1m AL870',
180000, 250000, 10,
'Cáp Cuktech Type-C to Lightning dài 1m AL870.jpg');


-- =========================================
-- PRODUCT RATING 
-- =========================================

UPDATE products
SET
    display_rating = CASE id
        WHEN 1 THEN 4.9
        WHEN 2 THEN 4.8
        WHEN 3 THEN 5.0
        WHEN 4 THEN 4.7
        WHEN 5 THEN 4.9
        WHEN 6 THEN 4.8
        WHEN 7 THEN 4.9
        WHEN 8 THEN 4.9
        WHEN 9 THEN 5.0
        WHEN 10 THEN 4.9
        WHEN 11 THEN 4.9
        WHEN 12 THEN 4.7
        WHEN 13 THEN 5.0
        WHEN 14 THEN 4.9
        WHEN 15 THEN 4.7
        WHEN 16 THEN 4.8
        WHEN 17 THEN 5.0
        WHEN 18 THEN 4.9
        WHEN 19 THEN 4.6
        WHEN 20 THEN 4.9
        WHEN 21 THEN 4.8
        WHEN 22 THEN 5.0
        WHEN 23 THEN 4.7
        WHEN 24 THEN 4.9
        WHEN 25 THEN 4.9
        ELSE display_rating
    END,

    display_review_count = CASE id
        WHEN 1 THEN 248
        WHEN 2 THEN 132
        WHEN 3 THEN 89
        WHEN 4 THEN 110
        WHEN 5 THEN 312
        WHEN 6 THEN 420
        WHEN 7 THEN 190
        WHEN 8 THEN 142
        WHEN 9 THEN 215
        WHEN 10 THEN 77
        WHEN 11 THEN 112
        WHEN 12 THEN 64
        WHEN 13 THEN 89
        WHEN 14 THEN 104
        WHEN 15 THEN 64
        WHEN 16 THEN 45
        WHEN 17 THEN 32
        WHEN 18 THEN 95
        WHEN 19 THEN 81
        WHEN 20 THEN 142
        WHEN 21 THEN 99
        WHEN 22 THEN 215
        WHEN 23 THEN 340
        WHEN 24 THEN 77
        WHEN 25 THEN 118
        ELSE display_review_count
    END
WHERE id BETWEEN 1 AND 25;

-- =========================================
-- KIEM TRA DANH GIA SAN PHAM
-- =========================================

SELECT
    id,
    name,
    display_rating,
    display_review_count
FROM products
ORDER BY id;


-- =========================================
-- SAMPLE PRODUCT REVIEWS
-- =========================================

INSERT INTO reviews
(user_id, product_id, rating, comment)
VALUES
(1, 1, 5, 'iPhone 18 Pro Max thiet ke dep, hieu nang manh, camera sac net.'),

(1, 2, 5, 'MacBook Air M2 mong nhe, pin lau, phu hop hoc tap va lam viec.'),

(1, 3, 5, 'Tai nghe chong on tot, am thanh ro rang, deo thoai mai.'),

(1, 4, 5, 'Apple Watch Series 10 thiet ke dep, nhieu tinh nang tien loi.'),

(1, 5, 5, 'AirPods Pro 2 am thanh hay, chong on tot, ket noi nhanh.'),

(1, 6, 5, 'Cap sac Type-C to Lightning su dung on dinh, sac nhanh.'),

(1, 7, 5, 'Samsung Galaxy S25 Ultra chup anh dep, man hinh sac net.'),

(1, 8, 5, 'Man hinh Dell UltraSharp 4K hien thi ro net, mau sac dep.'),

(1, 9, 5, 'Chuot Logitech G Pro X Superlight nhe, di chuyen chinh xac.'),

(1, 10, 5, 'Pin sac du phong Ugreen gon nhe, sac nhanh va tien loi.'),

(1, 11, 5, 'iPad Air M3 hoat dong muot, phu hop hoc tap va giai tri.'),

(1, 12, 5, 'Loa JBL Charge 6 am thanh manh, bass tot, chong nuoc.'),

(1, 13, 5, 'AirPods Max mau hong dep, am thanh tot, chong on hieu qua.'),

(1, 14, 5, 'Apple AirPods Max am thanh chat luong, thiet ke cao cap.'),

(1, 15, 5, 'Loa JBL Charge 6 chong nuoc tot, am thanh ro rang.'),

(1, 16, 5, 'Loa Edifier QD35 co den LED dep, am thanh song dong.'),

(1, 17, 5, 'Bo loa KEF LSX II LT am thanh chi tiet, thiet ke sang trong.'),

(1, 18, 5, 'Apple Watch Series 10 su dung muot, man hinh hien thi dep.'),

(1, 19, 5, 'REDMI Watch 6 pin tot, thiet ke dep, gia hop ly.'),

(1, 20, 5, 'Man hinh Dell UltraSharp 4K phu hop lam viec do hoa.'),

(1, 21, 5, 'Man hinh LG UltraGear 144Hz choi game muot, phan hoi nhanh.'),

(1, 22, 5, 'Chuot Logitech G Pro X Superlight nhe, cam bien chinh xac.'),

(1, 23, 5, 'Chuot Logitech G304 ket noi on dinh, pin lau, gia hop ly.'),

(1, 24, 5, 'Pin sac Ugreen MagFlow sac nhanh, thiet ke nho gon.'),

(1, 25, 5, 'Cap Cuktech Type-C to Lightning chac chan, truyen dien on dinh.');

-- =========================================
-- DATABASE VERIFICATION
-- =========================================

SELECT * FROM categories;
SELECT * FROM products;
SELECT * FROM users;
SELECT * FROM reviews;

-- =========================================
-- PRODUCT RATING STATISTICS
-- =========================================

SELECT
    p.id,
    p.name,
    COUNT(r.id) AS total_reviews,
    COALESCE(
        ROUND(AVG(r.rating), 1), 0
    ) AS average_rating
FROM products p
LEFT JOIN reviews r
    ON p.id = r.product_id
GROUP BY p.id, p.name
ORDER BY average_rating DESC;

-- =========================================
-- INVENTORY STATISTICS
-- =========================================

SELECT
    p.id,
    p.name,
    c.name AS category_name,
    p.price,
    p.quantity
FROM products p
LEFT JOIN categories c
    ON p.category_id = c.id
ORDER BY p.quantity ASC;
-- =========================================
-- SALES STATISTICS
-- =========================================

SELECT
    COUNT(*) AS total_orders,
    COALESCE(
        SUM(total_money),
        0
    ) AS total_revenue
FROM orders
WHERE status = 'Completed';


