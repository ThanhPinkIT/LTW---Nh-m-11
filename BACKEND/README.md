# 🛒 Website Bán Hàng Công Nghệ (PHP & MySQL)

Website bán hàng trực tuyến được xây dựng bằng ngôn ngữ **PHP thuần**, kết hợp với cơ sở dữ liệu **MySQL** và giao diện **Bootstrap 5**. Dự án hỗ trợ đầy đủ các tính năng của một trang web thương mại điện tử cơ bản, chia làm 2 phân hệ rõ ràng: Khách hàng (Frontend) và Quản trị viên (Backend Admin).

---

## 🚀 Tính Năng Chính

### 👥 Phân hệ Khách hàng (Frontend)
* **Trang chủ:** Hiển thị Banner quảng cáo và danh sách sản phẩm nổi bật.
* **Danh sách sản phẩm:** Hỗ trợ tìm kiếm sản phẩm theo tên, bộ lọc layout lưới (Grid) không vỡ khung.
* **Giỏ hàng (Cart):** Thêm sản phẩm vào giỏ hàng, cập nhật số lượng, tự động tính tổng tiền.
* **Tài khoản:** Đăng ký thành viên, Đăng nhập hệ thống, Xem lịch sử đơn hàng cá nhân, Cập nhật hồ sơ.

### 🛡️ Phân hệ Quản trị (Backend Admin Panel)
* **Dashboard:** Thống kê tổng quan hệ thống.
* **Quản lý sản phẩm:** Xem danh sách, Thêm mới (hỗ trợ upload ảnh), Sửa thông tin (giữ/đổi ảnh), Xóa sản phẩm.
* **Quản lý danh mục:** Thêm, sửa, xóa danh mục sản phẩm.
* **Quản lý đơn hàng:** Tiếp nhận, cập nhật trạng thái và theo dõi các đơn đặt hàng từ khách.
* **Quản lý người dùng:** Xem và quản lý danh sách thành viên/khách hàng.

---

## 🛠 Yêu Cầu Hệ Thống

* **Web Server:** Apache (Khuyên dùng XAMPP, WAMP hoặc Laragon)
* **PHP:** Phiên bản 7.4 trở lên (khuyên dùng PHP 8.x).
* **Cơ Sở Dữ Liệu:** MySQL hoặc MariaDB.
* **Trình duyệt:** Cập nhật bản mới nhất của Chrome, Firefox, Edge, Safari.

---

## ⚙️ Hướng Dẫn Cài Đặt

1. **Tải mã nguồn:** Clone repository hoặc tải mã nguồn về và giải nén vào thư mục root của web server (ví dụ: `C:\xampp\htdocs\web_ban_hang` đối với XAMPP).
2. **Khởi động server:** Bật **Apache** và **MySQL** trên XAMPP Control Panel.
3. **Cài đặt Cơ sở dữ liệu:**
   - Truy cập vào `http://localhost/phpmyadmin`
   - Tạo một database mới với tên tương ứng (ví dụ: `web_ban_hang`) và chọn Collation là `utf8mb4_unicode_ci`.
   - Tìm thư mục `database/` trong dự án và Import file `.sql` vào database vừa tạo.
4. **Cấu hình kết nối CSDL:**
   - Mở file cấu hình kết nối (thường nằm ở `config/db.php` hoặc tương tự).
   - Chỉnh sửa thông số (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`) cho khớp với cấu hình MySQL trên máy bạn.
5. **Chạy ứng dụng:**
   - Giao diện Khách hàng: `http://localhost/web_ban_hang`
   - Giao diện Admin: `http://localhost/web_ban_hang/admin`

---

## 📂 Cấu Trúc Thư Mục Dự Án

```text
web_ban_hang/
├── admin/                  # Phân hệ quản trị viên (Backend)
├── api/                    # API endpoints cho các xử lý Ajax/Fetch
├── assets/                 # Chứa tài nguyên tĩnh: CSS, JS, Images, Fonts
├── config/                 # Các file cấu hình hệ thống (như kết nối database)
├── database/               # Chứa script SQL để khởi tạo cơ sở dữ liệu
├── includes/               # Các file layout dùng chung (header, footer, sidebar)
├── uploads/                # Thư mục lưu trữ hình ảnh sản phẩm/người dùng tải lên
├── cart.php                # Xử lý trang giỏ hàng
├── checkout.php            # Xử lý trang thanh toán, đặt hàng
├── history.php             # Hiển thị lịch sử mua hàng của user
├── index.php               # Trang chủ Frontend
├── login.php               # Xử lý đăng nhập
├── logout.php              # Xử lý đăng xuất
├── product-detail.php      # Trang xem chi tiết một sản phẩm
├── products.php            # Trang hiển thị danh sách sản phẩm / kết quả tìm kiếm
├── profile.php             # Quản lý thông tin cá nhân của người dùng
├── register.php            # Xử lý đăng ký tài khoản mới
└── README.md               # File tài liệu hướng dẫn (File này)
```

---

## 🤝 Đóng Góp (Contributing)
Mọi đóng góp nhằm cải thiện và phát triển dự án đều được chào đón. Bạn có thể tạo Issues để báo lỗi hoặc gửi Pull Requests với các tính năng mới.

## 📝 Giấy Phép (License)
Dự án được phân phối dưới giấy phép **MIT License**. Bạn có quyền tự do sử dụng, chỉnh sửa và phân phối.
