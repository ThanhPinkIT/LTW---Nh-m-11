-- =====================================================
-- DỮ LIỆU DEMO CHO DASHBOARD (chạy 1 lần trong phpMyAdmin,
-- chọn database web_ban_hang). Có thể chạy lại: tự xóa dữ liệu demo cũ.
-- Đơn demo được đánh dấu payment_method = 'DEMO'.
-- Xóa sạch demo: DELETE FROM orders WHERE payment_method='DEMO';
-- =====================================================
DELETE FROM orders WHERE payment_method = 'DEMO';

-- 1) tạo đơn (mỗi user 40 đơn, nhiều đơn gần đây)
INSERT INTO orders (user_id, total_money, payment_method, status, created_at)
SELECT u.id, 0, 'DEMO',
       ELT(1 + FLOOR(RAND()*10), 'Completed','Completed','Completed','Completed','Completed',
                                  'Pending','Processing','Shipping','Shipping','Cancelled'),
       NOW() - INTERVAL FLOOR(POW(RAND(), 2) * 330) DAY - INTERVAL FLOOR(RAND() * 80000) SECOND
FROM users u
CROSS JOIN (SELECT a.n + b.n * 10 AS n
            FROM (SELECT 0 n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                  UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a
            CROSS JOIN (SELECT 0 n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) b) seq;

-- đơn cũ hơn 7 ngày thì cho hoàn thành hết (trừ đơn hủy)
UPDATE orders SET status = 'Completed'
WHERE payment_method = 'DEMO' AND created_at < NOW() - INTERVAL 7 DAY
  AND status IN ('Pending','Processing','Shipping');

-- 2) mỗi đơn khoảng 3 sản phẩm, có sản phẩm bán nhiều hơn cho top rõ ràng
INSERT INTO order_details (order_id, product_id, quantity, price)
SELECT o.id, p.id, 1 + FLOOR(RAND() * 3), p.price
FROM orders o
CROSS JOIN products p
CROSS JOIN (SELECT COUNT(*) c FROM products) pc
WHERE o.payment_method = 'DEMO'
  AND RAND() < (3 / pc.c) * (1.8 - (p.id % 5) * 0.35);

-- 3) cập nhật tổng tiền, xóa đơn không có sản phẩm
UPDATE orders o
JOIN (SELECT order_id, SUM(quantity * price) t FROM order_details GROUP BY order_id) d ON d.order_id = o.id
SET o.total_money = d.t
WHERE o.payment_method = 'DEMO';

DELETE FROM orders WHERE payment_method = 'DEMO' AND total_money = 0;
