-- =====================================================
-- THÔNG SỐ KỸ THUẬT CHO 25 SẢN PHẨM CÓ SẴN
-- Chạy 1 lần trong phpMyAdmin (chọn database web_ban_hang).
-- File này tạo bảng product_specs (nếu chưa có), XÓA thông số cũ của
-- 25 sản phẩm mẫu rồi nạp lại. Có thể chạy lại nhiều lần.
-- Sản phẩm bạn tự thêm bằng trang admin không bị ảnh hưởng.
-- Sản phẩm được nhận theo tên file ảnh, nên các sản phẩm trùng ảnh
-- (ví dụ 2 cái Apple Watch) đều có thông số.
-- =====================================================

CREATE TABLE IF NOT EXISTS product_specs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    spec_name VARCHAR(100) NOT NULL,
    spec_value VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- xóa thông số cũ của các sản phẩm mẫu
DELETE ps FROM product_specs ps
JOIN products p ON p.id = ps.product_id
WHERE p.image IN (
    'iphone18promax.jpg',
    'macbookm3.jpg',
    'Apple AirPods Max.jpg',
    'Apple AirPods Max hồng.jpg',
    'applewatch10.jpg',
    'airpodspro2.jpg',
    'Cáp Cuktech Type-C to Lightning dài 1m AL870.jpg',
    's25ultra.jpg',
    'dellultrasharp.jpg',
    'lgultragear.jpg',
    'logitechgprox.jpg',
    'g304.jpg',
    'Pin sạc dự phòng Magsafe Ugreen MagFlow 65958 10000mAh 25W.jpg',
    'ipadairm3.jpg',
    'Loa Bluetooth JBL Charge 6.jpg',
    'Loa Bluetooth Edifier QD35.jpg',
    'Loa KEF LSX II LT - White.jpg',
    'REDMI Watch 6.jpg'
);

-- ---------- nạp thông số mới ----------
-- iphone18promax.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Màn hình' AS n, '6.9 inch, Super Retina XDR OLED' AS v, 1 AS o
  UNION ALL SELECT 'Độ phân giải' AS n, '2868 x 1320 pixel (460 ppi)' AS v, 2 AS o
  UNION ALL SELECT 'Tần số quét' AS n, 'ProMotion, tối đa 120Hz' AS v, 3 AS o
  UNION ALL SELECT 'Chip' AS n, 'Apple A20 Pro (CPU 6 nhân, GPU 7 nhân)' AS v, 4 AS o
  UNION ALL SELECT 'Dung lượng' AS n, '256GB' AS v, 5 AS o
  UNION ALL SELECT 'Camera sau' AS n, '3 camera 48MP: chính, siêu rộng, tele 4x' AS v, 6 AS o
  UNION ALL SELECT 'Camera trước' AS n, '18MP Center Stage' AS v, 7 AS o
  UNION ALL SELECT 'Pin' AS n, 'Dùng thường ngày đến 30 giờ, xem video đến 45 giờ' AS v, 8 AS o
  UNION ALL SELECT 'Sạc' AS n, 'USB-C (USB 3, 10Gb/s), MagSafe và Qi2 đến 25W' AS v, 9 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Wi-Fi 7, Bluetooth 6, 5G' AS v, 10 AS o
  UNION ALL SELECT 'SIM' AS n, '2 eSIM (không dùng SIM vật lý)' AS v, 11 AS o
  UNION ALL SELECT 'Chống nước, bụi' AS n, 'IP68' AS v, 12 AS o
  UNION ALL SELECT 'Chất liệu' AS n, 'Khung nhôm nguyên khối, mặt kính Ceramic Shield 2' AS v, 13 AS o
  UNION ALL SELECT 'Bảo mật' AS n, 'Face ID' AS v, 14 AS o
  UNION ALL SELECT 'Kích thước' AS n, '163.4 x 78.0 x 8.75 mm' AS v, 15 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '249 g' AS v, 16 AS o
  UNION ALL SELECT 'Hệ điều hành' AS n, 'iOS' AS v, 17 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 18 AS o
) t
WHERE p.image = 'iphone18promax.jpg';

-- macbookm3.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Màn hình' AS n, '13.6 inch, Liquid Retina' AS v, 1 AS o
  UNION ALL SELECT 'Độ phân giải' AS n, '2560 x 1664 pixel, 500 nits, dải màu P3' AS v, 2 AS o
  UNION ALL SELECT 'Chip' AS n, 'Apple M2 (CPU 8 nhân, GPU 8 nhân)' AS v, 3 AS o
  UNION ALL SELECT 'RAM' AS n, '8GB' AS v, 4 AS o
  UNION ALL SELECT 'Ổ cứng' AS n, 'SSD 256GB' AS v, 5 AS o
  UNION ALL SELECT 'Camera' AS n, 'FaceTime HD 1080p' AS v, 6 AS o
  UNION ALL SELECT 'Âm thanh' AS n, '4 loa, 3 micro, Spatial Audio' AS v, 7 AS o
  UNION ALL SELECT 'Cổng kết nối' AS n, '2 x Thunderbolt / USB 4, MagSafe 3, jack tai nghe 3.5 mm' AS v, 8 AS o
  UNION ALL SELECT 'Kết nối không dây' AS n, 'Wi-Fi 6, Bluetooth 5.0' AS v, 9 AS o
  UNION ALL SELECT 'Bàn phím' AS n, 'Magic Keyboard có đèn nền, Touch ID' AS v, 10 AS o
  UNION ALL SELECT 'Pin' AS n, 'Xem video đến 18 giờ' AS v, 11 AS o
  UNION ALL SELECT 'Kích thước' AS n, '30.41 x 21.5 x 1.13 cm' AS v, 12 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '1.24 kg' AS v, 13 AS o
  UNION ALL SELECT 'Hệ điều hành' AS n, 'macOS' AS v, 14 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 15 AS o
) t
WHERE p.image = 'macbookm3.jpg';

-- Apple AirPods Max.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Kiểu tai nghe' AS n, 'Chụp tai không dây' AS v, 1 AS o
  UNION ALL SELECT 'Driver' AS n, 'Apple 40 mm' AS v, 2 AS o
  UNION ALL SELECT 'Chip' AS n, 'Apple H1 ở mỗi bên tai' AS v, 3 AS o
  UNION ALL SELECT 'Chống ồn' AS n, 'Chống ồn chủ động (ANC) và chế độ Xuyên âm' AS v, 4 AS o
  UNION ALL SELECT 'Âm thanh' AS n, 'Âm thanh không gian, theo dõi chuyển động đầu' AS v, 5 AS o
  UNION ALL SELECT 'Thời lượng pin' AS n, 'Đến 20 giờ khi bật chống ồn' AS v, 6 AS o
  UNION ALL SELECT 'Sạc nhanh' AS n, 'Sạc 5 phút dùng khoảng 1.5 giờ' AS v, 7 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Bluetooth 5.0' AS v, 8 AS o
  UNION ALL SELECT 'Điều khiển' AS n, 'Núm Digital Crown và nút điều khiển tiếng ồn' AS v, 9 AS o
  UNION ALL SELECT 'Tìm thiết bị' AS n, 'Hỗ trợ Tìm (Find My)' AS v, 10 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '384.8 g' AS v, 11 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 12 AS o
) t
WHERE p.image = 'Apple AirPods Max.jpg';

-- Apple AirPods Max hồng.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Kiểu tai nghe' AS n, 'Chụp tai không dây' AS v, 1 AS o
  UNION ALL SELECT 'Màu sắc' AS n, 'Hồng' AS v, 2 AS o
  UNION ALL SELECT 'Driver' AS n, 'Apple 40 mm' AS v, 3 AS o
  UNION ALL SELECT 'Chip' AS n, 'Apple H1 ở mỗi bên tai' AS v, 4 AS o
  UNION ALL SELECT 'Chống ồn' AS n, 'Chống ồn chủ động (ANC) và chế độ Xuyên âm' AS v, 5 AS o
  UNION ALL SELECT 'Âm thanh' AS n, 'Âm thanh không gian, theo dõi chuyển động đầu' AS v, 6 AS o
  UNION ALL SELECT 'Thời lượng pin' AS n, 'Đến 20 giờ khi bật chống ồn' AS v, 7 AS o
  UNION ALL SELECT 'Sạc nhanh' AS n, 'Sạc 5 phút dùng khoảng 1.5 giờ' AS v, 8 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Bluetooth 5.0' AS v, 9 AS o
  UNION ALL SELECT 'Điều khiển' AS n, 'Núm Digital Crown và nút điều khiển tiếng ồn' AS v, 10 AS o
  UNION ALL SELECT 'Tìm thiết bị' AS n, 'Hỗ trợ Tìm (Find My)' AS v, 11 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '384.8 g' AS v, 12 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 13 AS o
) t
WHERE p.image = 'Apple AirPods Max hồng.jpg';

-- applewatch10.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Màn hình' AS n, 'OLED LTPO, luôn hiển thị (Always-On)' AS v, 1 AS o
  UNION ALL SELECT 'Kích thước vỏ' AS n, '42 mm / 46 mm' AS v, 2 AS o
  UNION ALL SELECT 'Độ sáng tối đa' AS n, '2000 nits' AS v, 3 AS o
  UNION ALL SELECT 'Chip' AS n, 'Apple S10' AS v, 4 AS o
  UNION ALL SELECT 'Bộ nhớ' AS n, '64GB' AS v, 5 AS o
  UNION ALL SELECT 'Pin' AS n, 'Đến 18 giờ, sạc nhanh 80% trong khoảng 30 phút' AS v, 6 AS o
  UNION ALL SELECT 'Chống nước' AS n, '50 m' AS v, 7 AS o
  UNION ALL SELECT 'Cảm biến' AS n, 'Nhịp tim quang học, điện tâm đồ (ECG), nhiệt độ cổ tay, la bàn, đo độ sâu' AS v, 8 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Bluetooth 5.3, Wi-Fi, GPS' AS v, 9 AS o
  UNION ALL SELECT 'Hệ điều hành' AS n, 'watchOS' AS v, 10 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 11 AS o
) t
WHERE p.image = 'applewatch10.jpg';

-- airpodspro2.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Chip' AS n, 'Apple H2' AS v, 1 AS o
  UNION ALL SELECT 'Chống ồn' AS n, 'Chống ồn chủ động (ANC) và Xuyên âm thích ứng' AS v, 2 AS o
  UNION ALL SELECT 'Âm thanh' AS n, 'Âm thanh không gian cá nhân hóa' AS v, 3 AS o
  UNION ALL SELECT 'Thời lượng pin' AS n, 'Đến 6 giờ khi bật chống ồn, tổng đến 30 giờ cùng hộp sạc' AS v, 4 AS o
  UNION ALL SELECT 'Chống nước, bụi' AS n, 'IP54 (tai nghe và hộp sạc)' AS v, 5 AS o
  UNION ALL SELECT 'Hộp sạc' AS n, 'USB-C, sạc MagSafe, sạc được bằng đế sạc Apple Watch' AS v, 6 AS o
  UNION ALL SELECT 'Điều khiển' AS n, 'Cảm ứng lực trên thân tai nghe, vuốt chỉnh âm lượng' AS v, 7 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Bluetooth 5.3' AS v, 8 AS o
  UNION ALL SELECT 'Đệm tai' AS n, '4 kích cỡ (XS, S, M, L)' AS v, 9 AS o
  UNION ALL SELECT 'Tìm thiết bị' AS n, 'Hỗ trợ Tìm (Find My), hộp sạc có loa phát tiếng' AS v, 10 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 11 AS o
) t
WHERE p.image = 'airpodspro2.jpg';

-- Cáp Cuktech Type-C to Lightning dài 1m AL870.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Loại cáp' AS n, 'USB-C to Lightning' AS v, 1 AS o
  UNION ALL SELECT 'Chiều dài' AS n, '1 m' AS v, 2 AS o
  UNION ALL SELECT 'Công suất sạc' AS n, '30W, chuẩn sạc nhanh PD 3.0' AS v, 3 AS o
  UNION ALL SELECT 'Truyền dữ liệu' AS n, '480 Mbps' AS v, 4 AS o
  UNION ALL SELECT 'Chứng nhận' AS n, 'Apple MFi' AS v, 5 AS o
  UNION ALL SELECT 'Chất liệu vỏ' AS n, 'TPE' AS v, 6 AS o
  UNION ALL SELECT 'Độ bền' AS n, 'Chịu khoảng 10.000 lần gập' AS v, 7 AS o
  UNION ALL SELECT 'Màu sắc' AS n, 'Trắng' AS v, 8 AS o
  UNION ALL SELECT 'Tương thích' AS n, 'iPhone, iPad dùng cổng Lightning' AS v, 9 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 10 AS o
) t
WHERE p.image = 'Cáp Cuktech Type-C to Lightning dài 1m AL870.jpg';

-- s25ultra.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Màn hình' AS n, '6.9 inch, Dynamic AMOLED 2X' AS v, 1 AS o
  UNION ALL SELECT 'Độ phân giải' AS n, 'QHD+ (3120 x 1440)' AS v, 2 AS o
  UNION ALL SELECT 'Tần số quét' AS n, '1 – 120Hz' AS v, 3 AS o
  UNION ALL SELECT 'Độ sáng tối đa' AS n, '2600 nits' AS v, 4 AS o
  UNION ALL SELECT 'Kính' AS n, 'Corning Gorilla Armor 2' AS v, 5 AS o
  UNION ALL SELECT 'Chip' AS n, 'Snapdragon 8 Elite for Galaxy' AS v, 6 AS o
  UNION ALL SELECT 'RAM' AS n, '12GB' AS v, 7 AS o
  UNION ALL SELECT 'Bộ nhớ trong' AS n, '256GB' AS v, 8 AS o
  UNION ALL SELECT 'Camera sau' AS n, '200MP chính + 50MP siêu rộng + 10MP tele 3x + 50MP tele 5x' AS v, 9 AS o
  UNION ALL SELECT 'Camera trước' AS n, '12MP' AS v, 10 AS o
  UNION ALL SELECT 'Pin' AS n, '5.000 mAh, sạc nhanh 45W, sạc không dây 15W' AS v, 11 AS o
  UNION ALL SELECT 'Khung máy' AS n, 'Titan' AS v, 12 AS o
  UNION ALL SELECT 'Chống nước, bụi' AS n, 'IP68' AS v, 13 AS o
  UNION ALL SELECT 'Bút S Pen' AS n, 'Tích hợp sẵn' AS v, 14 AS o
  UNION ALL SELECT 'Kết nối' AS n, '5G, Wi-Fi 7, Bluetooth 5.4, NFC, UWB' AS v, 15 AS o
  UNION ALL SELECT 'SIM' AS n, '2 nano SIM + eSIM' AS v, 16 AS o
  UNION ALL SELECT 'Kích thước' AS n, '162.8 x 77.6 x 8.2 mm' AS v, 17 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '218 g' AS v, 18 AS o
  UNION ALL SELECT 'Hệ điều hành' AS n, 'Android (One UI)' AS v, 19 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 20 AS o
) t
WHERE p.image = 's25ultra.jpg';

-- dellultrasharp.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Kích thước' AS n, '27 inch, tỷ lệ 16:9' AS v, 1 AS o
  UNION ALL SELECT 'Độ phân giải' AS n, '4K UHD (3840 x 2160)' AS v, 2 AS o
  UNION ALL SELECT 'Tấm nền' AS n, 'IPS' AS v, 3 AS o
  UNION ALL SELECT 'Độ phủ màu' AS n, '100% sRGB' AS v, 4 AS o
  UNION ALL SELECT 'Hiệu chuẩn màu' AS n, 'Hiệu chuẩn sẵn từ nhà máy' AS v, 5 AS o
  UNION ALL SELECT 'Cổng kết nối' AS n, 'HDMI, DisplayPort, USB-C, cổng USB' AS v, 6 AS o
  UNION ALL SELECT 'Chân đế' AS n, 'Nghiêng, xoay, nâng hạ chiều cao' AS v, 7 AS o
  UNION ALL SELECT 'Treo tường' AS n, 'Chuẩn VESA 100 x 100 mm' AS v, 8 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 9 AS o
) t
WHERE p.image = 'dellultrasharp.jpg';

-- lgultragear.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Loại màn hình' AS n, 'Gaming' AS v, 1 AS o
  UNION ALL SELECT 'Kích thước' AS n, '27 inch' AS v, 2 AS o
  UNION ALL SELECT 'Độ phân giải' AS n, 'QHD (2560 x 1440)' AS v, 3 AS o
  UNION ALL SELECT 'Tấm nền' AS n, 'IPS' AS v, 4 AS o
  UNION ALL SELECT 'Tần số quét' AS n, '144Hz' AS v, 5 AS o
  UNION ALL SELECT 'Thời gian phản hồi' AS n, '1ms' AS v, 6 AS o
  UNION ALL SELECT 'Đồng bộ hình ảnh' AS n, 'AMD FreeSync, tương thích NVIDIA G-SYNC' AS v, 7 AS o
  UNION ALL SELECT 'Cổng kết nối' AS n, 'HDMI, DisplayPort, jack tai nghe' AS v, 8 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 9 AS o
) t
WHERE p.image = 'lgultragear.jpg';

-- logitechgprox.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Cảm biến' AS n, 'HERO 25K' AS v, 1 AS o
  UNION ALL SELECT 'Độ nhạy (DPI)' AS n, '100 – 25.600' AS v, 2 AS o
  UNION ALL SELECT 'Tốc độ theo dõi' AS n, 'Trên 400 IPS' AS v, 3 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, 'Dưới 63 g' AS v, 4 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Không dây LIGHTSPEED 2.4GHz' AS v, 5 AS o
  UNION ALL SELECT 'Thời lượng pin' AS n, 'Đến 70 giờ' AS v, 6 AS o
  UNION ALL SELECT 'Cổng sạc' AS n, 'Micro-USB' AS v, 7 AS o
  UNION ALL SELECT 'Số nút' AS n, '5 nút, lập trình được' AS v, 8 AS o
  UNION ALL SELECT 'Công tắc' AS n, 'Lai quang – cơ LIGHTFORCE' AS v, 9 AS o
  UNION ALL SELECT 'Chân chuột' AS n, '100% PTFE' AS v, 10 AS o
  UNION ALL SELECT 'Tần số báo cáo' AS n, '1000Hz' AS v, 11 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 12 AS o
) t
WHERE p.image = 'logitechgprox.jpg';

-- g304.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Cảm biến' AS n, 'HERO' AS v, 1 AS o
  UNION ALL SELECT 'Độ nhạy (DPI)' AS n, '200 – 12.000' AS v, 2 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Không dây LIGHTSPEED 2.4GHz' AS v, 3 AS o
  UNION ALL SELECT 'Nguồn' AS n, '1 pin AA' AS v, 4 AS o
  UNION ALL SELECT 'Thời lượng pin' AS n, 'Đến khoảng 250 giờ (chế độ hiệu năng)' AS v, 5 AS o
  UNION ALL SELECT 'Số nút' AS n, '6 nút, lập trình được' AS v, 6 AS o
  UNION ALL SELECT 'Tần số báo cáo' AS n, '1000Hz' AS v, 7 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, 'Khoảng 99 g' AS v, 8 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 9 AS o
) t
WHERE p.image = 'g304.jpg';

-- Pin sạc dự phòng Magsafe Ugreen MagFlow 65958 10000mAh 25W.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Model' AS n, 'Ugreen MagFlow 65958' AS v, 1 AS o
  UNION ALL SELECT 'Dung lượng' AS n, '10.000 mAh' AS v, 2 AS o
  UNION ALL SELECT 'Sạc không dây' AS n, 'Qi2 25W, nam châm tương thích MagSafe' AS v, 3 AS o
  UNION ALL SELECT 'Sạc có dây' AS n, 'USB-C 30W (PD), có sẵn cáp USB-C tích hợp dùng làm dây đeo' AS v, 4 AS o
  UNION ALL SELECT 'Tương thích' AS n, 'iPhone 12 đến iPhone 17 và thiết bị hỗ trợ Qi2' AS v, 5 AS o
  UNION ALL SELECT 'Hiển thị' AS n, 'Đèn LED báo phần trăm pin' AS v, 6 AS o
  UNION ALL SELECT 'Bảo vệ' AS n, '13 lớp bảo vệ: quá nhiệt, quá dòng, ngắn mạch' AS v, 7 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, 'Khoảng 245 g' AS v, 8 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 9 AS o
) t
WHERE p.image = 'Pin sạc dự phòng Magsafe Ugreen MagFlow 65958 10000mAh 25W.jpg';

-- ipadairm3.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Màn hình' AS n, '11 inch, Liquid Retina' AS v, 1 AS o
  UNION ALL SELECT 'Độ phân giải' AS n, '2360 x 1640 pixel (264 ppi), 500 nits, dải màu P3' AS v, 2 AS o
  UNION ALL SELECT 'Chip' AS n, 'Apple M3 (CPU 8 nhân, GPU 9 nhân)' AS v, 3 AS o
  UNION ALL SELECT 'RAM' AS n, '8GB' AS v, 4 AS o
  UNION ALL SELECT 'Dung lượng' AS n, '128GB đến 1TB, tùy phiên bản' AS v, 5 AS o
  UNION ALL SELECT 'Camera sau' AS n, '12MP' AS v, 6 AS o
  UNION ALL SELECT 'Camera trước' AS n, '12MP Center Stage (quay ngang)' AS v, 7 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Wi-Fi 6E, Bluetooth 5.3, 5G (bản Cellular)' AS v, 8 AS o
  UNION ALL SELECT 'Cổng' AS n, 'USB-C' AS v, 9 AS o
  UNION ALL SELECT 'Bảo mật' AS n, 'Touch ID' AS v, 10 AS o
  UNION ALL SELECT 'Phụ kiện' AS n, 'Apple Pencil Pro, Apple Pencil (USB-C), Magic Keyboard' AS v, 11 AS o
  UNION ALL SELECT 'Pin' AS n, 'Đến 10 giờ' AS v, 12 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, 'Khoảng 460 g (bản Wi-Fi)' AS v, 13 AS o
  UNION ALL SELECT 'Hệ điều hành' AS n, 'iPadOS' AS v, 14 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 15 AS o
) t
WHERE p.image = 'ipadairm3.jpg';

-- Loa Bluetooth JBL Charge 6.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Công suất' AS n, '45W tối đa (loa trầm 30W RMS, loa tép 10W RMS)' AS v, 1 AS o
  UNION ALL SELECT 'Loa' AS n, 'Loa trầm 53 x 93 mm, loa tép 20 mm' AS v, 2 AS o
  UNION ALL SELECT 'Dải tần' AS n, '56Hz – 20kHz' AS v, 3 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Bluetooth 5.4, hỗ trợ Auracast' AS v, 4 AS o
  UNION ALL SELECT 'Chống nước, bụi' AS n, 'IP68' AS v, 5 AS o
  UNION ALL SELECT 'Pin' AS n, '4.722 mAh, nghe nhạc đến 24 giờ (28 giờ khi bật Playtime Boost)' AS v, 6 AS o
  UNION ALL SELECT 'Thời gian sạc' AS n, 'Khoảng 3 giờ' AS v, 7 AS o
  UNION ALL SELECT 'Cổng' AS n, 'USB-C, nhận âm thanh lossless' AS v, 8 AS o
  UNION ALL SELECT 'Tính năng' AS n, 'Sạc ngược cho điện thoại (powerbank), ghép đôi stereo' AS v, 9 AS o
  UNION ALL SELECT 'Kích thước' AS n, '228.8 x 98.5 x 94 mm' AS v, 10 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '0.96 kg' AS v, 11 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 12 AS o
) t
WHERE p.image = 'Loa Bluetooth JBL Charge 6.jpg';

-- Loa Bluetooth Edifier QD35.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Loại loa' AS n, 'Loa để bàn 2.0, kết nối Bluetooth' AS v, 1 AS o
  UNION ALL SELECT 'Công suất' AS n, '40W RMS (loa trầm - trung 25W, loa tép 15W)' AS v, 2 AS o
  UNION ALL SELECT 'Loa' AS n, 'Loa trầm - trung 3 inch, loa tép màng dome 1 inch' AS v, 3 AS o
  UNION ALL SELECT 'Dải tần' AS n, '60Hz – 40kHz' AS v, 4 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Bluetooth 5.3 (hỗ trợ LDAC), USB-A, AUX 3.5 mm' AS v, 5 AS o
  UNION ALL SELECT 'Chất lượng âm thanh' AS n, 'Chứng nhận Hi-Res Audio' AS v, 6 AS o
  UNION ALL SELECT 'Sạc cho thiết bị khác' AS n, 'Cổng USB-A và USB-C, GaN đến 35W' AS v, 7 AS o
  UNION ALL SELECT 'Đèn' AS n, 'LED RGB, tùy chỉnh bằng ứng dụng Edifier Connect' AS v, 8 AS o
  UNION ALL SELECT 'Nguồn' AS n, 'Cắm điện AC' AS v, 9 AS o
  UNION ALL SELECT 'Kích thước' AS n, '277.8 x 164.8 x 141.7 mm' AS v, 10 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '2.64 kg' AS v, 11 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 12 AS o
) t
WHERE p.image = 'Loa Bluetooth Edifier QD35.jpg';

-- Loa KEF LSX II LT - White.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Loại loa' AS n, 'Cặp loa bookshelf không dây chủ động 2.0' AS v, 1 AS o
  UNION ALL SELECT 'Driver' AS n, 'Uni-Q: loa tép nhôm 19 mm, loa trầm - trung 115 mm (4.5 inch)' AS v, 2 AS o
  UNION ALL SELECT 'Công suất ampli' AS n, '200W tổng, Class D (mỗi loa: 70W loa trầm, 30W loa tép)' AS v, 3 AS o
  UNION ALL SELECT 'Dải tần' AS n, '49Hz – 47kHz (-6dB)' AS v, 4 AS o
  UNION ALL SELECT 'Âm lượng tối đa' AS n, '102 dB' AS v, 5 AS o
  UNION ALL SELECT 'Kết nối không dây' AS n, 'Wi-Fi, AirPlay 2, Chromecast, UPnP, Bluetooth 5.0' AS v, 6 AS o
  UNION ALL SELECT 'Ngõ vào' AS n, 'HDMI ARC, Optical, USB-C, Ethernet RJ45' AS v, 7 AS o
  UNION ALL SELECT 'Ngõ ra' AS n, 'Subwoofer (RCA)' AS v, 8 AS o
  UNION ALL SELECT 'Ứng dụng' AS n, 'KEF Connect' AS v, 9 AS o
  UNION ALL SELECT 'Điện năng tiêu thụ' AS n, '200W khi hoạt động, dưới 2W khi chờ' AS v, 10 AS o
  UNION ALL SELECT 'Màu sắc' AS n, 'Trắng (Stone White)' AS v, 11 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '6.8 kg (cả cặp)' AS v, 12 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 13 AS o
) t
WHERE p.image = 'Loa KEF LSX II LT - White.jpg';

-- REDMI Watch 6.jpg
INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
SELECT p.id, t.n, t.v, t.o FROM products p
JOIN (
  SELECT 'Màn hình' AS n, '2.07 inch AMOLED, 432 x 514 pixel (324 ppi)' AS v, 1 AS o
  UNION ALL SELECT 'Tần số quét, độ sáng' AS n, '60Hz, tối đa 2000 nits' AS v, 2 AS o
  UNION ALL SELECT 'Chất liệu' AS n, 'Khung nhôm, dày 9.9 mm' AS v, 3 AS o
  UNION ALL SELECT 'Kích thước' AS n, '46.5 x 40 x 9.9 mm' AS v, 4 AS o
  UNION ALL SELECT 'Trọng lượng' AS n, '31 g' AS v, 5 AS o
  UNION ALL SELECT 'Pin' AS n, '550 mAh, dùng đến 24 ngày (dùng nhẹ)' AS v, 6 AS o
  UNION ALL SELECT 'Chống nước' AS n, '5ATM (50 m)' AS v, 7 AS o
  UNION ALL SELECT 'Kết nối' AS n, 'Bluetooth 5.4, hỗ trợ nghe gọi qua Bluetooth' AS v, 8 AS o
  UNION ALL SELECT 'Định vị' AS n, 'GNSS độc lập (GPS, GLONASS, Galileo, BeiDou, QZSS)' AS v, 9 AS o
  UNION ALL SELECT 'Cảm biến' AS n, 'Nhịp tim, SpO2, gia tốc, con quay hồi chuyển, la bàn' AS v, 10 AS o
  UNION ALL SELECT 'Chế độ thể thao' AS n, 'Hơn 150 chế độ' AS v, 11 AS o
  UNION ALL SELECT 'Hệ điều hành' AS n, 'Xiaomi HyperOS 3, dùng được với Android và iOS' AS v, 12 AS o
  UNION ALL SELECT 'Bảo hành' AS n, '12 tháng' AS v, 13 AS o
) t
WHERE p.image = 'REDMI Watch 6.jpg';

-- Kiểm tra: mỗi sản phẩm phải có từ 9 thông số trở lên
-- SELECT p.id, p.name, COUNT(s.id) AS so_thong_so
-- FROM products p LEFT JOIN product_specs s ON s.product_id = p.id
-- GROUP BY p.id, p.name ORDER BY p.id;
