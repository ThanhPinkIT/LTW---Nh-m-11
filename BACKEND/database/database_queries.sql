USE web_ban_hang;
-- Tim san phan co ten chua chu Iphone
SELECT *
FROM products
WHERE name LIKE '%iPhone%';

-- 2. Tim san pham gia duoi 20 trieu
SELECT name, price
FROM products
WHERE price < 20000000;

-- 3. Dem tong so san pham
SELECT COUNT(*) AS tong_san_pham
FROM products;

-- 4. Thong ke hang ton kho
SELECT name, quantity
FROM products
ORDER BY quantity ASC;

-- 5. Tim san pham dat nhat
SELECT name, price
FROM products
ORDER BY price DESC
LIMIT 1;

-- 6. Thong ke diem danh gia trung binh
SELECT
    p.name AS ten_san_pham,
    COUNT(r.id) AS so_luot_danh_gia,
    ROUND(AVG(r.rating), 1) AS diem_trung_binh
FROM products p
LEFT JOIN reviews r
    ON p.id = r.product_id
GROUP BY p.id, p.name;

-- 7. Thong ke doanh thu don da hoan thanh
SELECT
    COUNT(*) AS tong_don_hang,
    COALESCE(SUM(total_money), 0) AS doanh_thu
FROM orders
WHERE status = 'Completed';

-- 8. Thong ke so san pham theo danh muc
SELECT
    c.name AS danh_muc,
    COUNT(p.id) AS so_san_pham
FROM categories c
LEFT JOIN products p
    ON c.id = p.category_id
GROUP BY c.id, c.name;

--9. Kiem tra danh gia san pham
SELECT
    p.name AS ten_san_pham,
    r.rating AS so_sao,
    r.comment AS binh_luan
FROM reviews r
JOIN products p
    ON r.product_id = p.id;

-- 10.Kiem tra khoa ngoai
SELECT
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'web_ban_hang'
AND TABLE_NAME = 'reviews'
AND REFERENCED_TABLE_NAME IS NOT NULL;
