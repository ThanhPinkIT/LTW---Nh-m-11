# LTW - Nhóm 11: Trang Web Bán Đồ Công Nghệ (NexTech)

Dự án Website bán hàng công nghệ được xây dựng với cấu trúc tách biệt giữa **Backend** và **Frontend**.

---

## 📁 Cấu trúc thư mục

```text
LTW---Nh-m-11/
├── BACKEND/                    # Máy chủ API & Trang Quản trị (Admin)
│   ├── admin/                  # Giao diện quản lý (Dashboard, sản phẩm, đơn hàng...)
│   ├── api/                    # RESTful APIs (auth, cart, product, order, user...)
│   ├── config/                 # Cấu hình kết nối MySQL (db.php)
│   ├── database/               # File SQL khởi tạo dữ liệu (shop.sql)
│   └── uploads/                # Hình ảnh sản phẩm
├── FONTEND/                    # Giao diện người dùng (Khách hàng)
│   ├── assets/
│   │   ├── css/                # Giao diện (style, login, profile, voucher...)
│   │   └── js/
│   │       ├── api.js          # Module tiện ích kết nối API Backend
│   │       ├── cart.js         # Xử lý giỏ hàng
│   │       └── voucher.js      # Xử lý voucher giảm giá
│   ├── includes/               # Header, Footer dùng chung
│   ├── uploads/                # Hình ảnh sản phẩm
│   ├── index.php               # Trang chủ
│   ├── products.php            # Danh sách sản phẩm & tìm kiếm
│   ├── product-detail.php      # Chi tiết sản phẩm & đánh giá
│   ├── cart.php                # Giỏ hàng
│   ├── checkout.php            # Thanh toán đơn hàng
│   ├── login.php               # Đăng nhập
│   ├── register.php            # Đăng ký
│   ├── profile.php             # Thông tin cá nhân
│   ├── history.php             # Lịch sử mua hàng
│   └── voucher.php             # Trung tâm voucher khuyến mãi
└── README.md
```

---

## 🚀 Hướng dẫn cài đặt & Chạy dự án

### 1. Yêu cầu hệ thống
- Cài đặt **XAMPP** (hỗ trợ PHP 8.x và MySQL/MariaDB).

### 2. Thiết lập
1. Sao chép toàn bộ thư mục dự án vào thư mục `htdocs` của XAMPP:
   ```text
   C:\xampp\htdocs\LTW_Web_ban_hang-main\
   ```
2. Mở **XAMPP Control Panel** và bấm **Start** cả 2 dịch vụ:
   - **Apache**
   - **MySQL**
3. Mở trình duyệt và truy cập `http://localhost/phpmyadmin`:
   - Tạo cơ sở dữ liệu mới với tên: `web_ban_hang`
   - Chọn database `web_ban_hang` vừa tạo ➜ chọn thẻ **Import (Nhập)**.
   - Chọn file `BACKEND/database/shop.sql` và bấm **Import**.

### 3. Truy cập Website
- **Trang khách hàng (Frontend)**: [http://localhost/LTW_Web_ban_hang-main/FONTEND/](http://localhost/LTW_Web_ban_hang-main/FONTEND/)
- **Trang quản trị (Admin Panel)**: [http://localhost/LTW_Web_ban_hang-main/BACKEND/admin/](http://localhost/LTW_Web_ban_hang-main/BACKEND/admin/)

### 4. Tài khoản đăng nhập mẫu
| Vai trò | Email / Tài khoản | Mật khẩu |
|---|---|---|
| **Admin** | `admin@gmail.com` (hoặc `admin`) | `password` |
| **Khách hàng** | Tự tạo tài khoản tại `FONTEND/register.php` | Mật khẩu đã đăng ký |
