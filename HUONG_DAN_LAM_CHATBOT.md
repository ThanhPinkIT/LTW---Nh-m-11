# HƯỚNG DẪN CHI TIẾT CÁCH XÂY DỰNG CHATBOT "TECH ASSISTANT" CHO WEBSITE BÁN HÀNG

Tài liệu này ghi lại toàn bộ quy trình, kiến trúc kỹ thuật và từng bước code để xây dựng con chatbot **Tech Assistant** (Rule-based Chatbot) cho website bán hàng công nghệ NexTech.

---

## I. TỔNG QUAN KIẾN TRÚC

```
                      +---------------------------------------+
                      |               NGƯỜI DÙNG              |
                      +---------------------------------------+
                                          |
                              Gửi câu hỏi / Nhấn gợi ý
                                          v
+-----------------------------------------------------------------------------------+
| FRONTEND (Giao diện khách hàng)                                                   |
| - assets/css/chatbot.css : Định dạng nút nổi, cửa sổ chat, thẻ sản phẩm, chips     |
| - assets/js/chatbot.js   : Bắt sự kiện, tạo DOM, gọi fetch(), chống XSS, render    |
| - includes/footer.php    : Nơi nhúng chatbot vào mọi trang của website            |
+-----------------------------------------------------------------------------------+
                                          |
                        Gọi API POST kèm JSON { message: "..." }
                                          v
+-----------------------------------------------------------------------------------+
| BACKEND API (Xử lý quy tắc & dữ liệu)                                             |
| - api/chatbot/chat.php                                                            |
|   1. Chuẩn hóa tiếng Việt (loại bỏ dấu: "điện thoại" -> "dien thoai")            |
|   2. Phân tích ngữ cảnh & bóc tách số tiền (15 triệu -> 15.000.000 đ)             |
|   3. Phân loại câu hỏi vào 6 nhóm kịch bản                                        |
|   4. Truy vấn MySQL bằng Prepared Statements (chống SQL Injection)                |
|   5. Trả về kết quả JSON { reply, products, suggestions }                         |
+-----------------------------------------------------------------------------------+
                                          |
                                Truy vấn SQL an toàn
                                          v
+-----------------------------------------------------------------------------------+
| DATABASE (MySQL - web_ban_hang)                                                   |
| - Bảng products   : Lấy tên, giá bán, ảnh, mô tả, tồn kho...                       |
| - Bảng categories : Lấy danh mục Laptop, Điện thoại, Phụ kiện, Màn hình...        |
+-----------------------------------------------------------------------------------+
```

---

## II. DANH SÁCH CÁC FILE ĐÃ TRIỂN KHAI

| File | Vị trí | Chức năng |
|---|---|---|
| `chat.php` | `BACKEND/api/chatbot/chat.php` | API xử lý kịch bản, chuẩn hóa tiếng Việt, kết nối MySQL |
| `chatbot.css` | `FRONTEND/assets/css/chatbot.css` | Giao diện nút nổi, cửa sổ chat, thẻ sản phẩm, responsive |
| `chatbot.js` | `FRONTEND/assets/js/chatbot.js` | Tạo DOM, gửi/nhận tin qua Fetch API, render Markdown & HTML an toàn |
| `footer.php` | `FRONTEND/includes/footer.php` | Nhúng file CSS và JS vào hệ thống website |

---

## III. CÁC BƯỚC THỰC HIỆN CHI TIẾT

### BƯỚC 1: XÂY DỰNG BACKEND PHP HANDLER (`BACKEND/api/chatbot/chat.php`)

Backend hoạt động theo mô hình **Rule-based NLP (Xử lý ngôn ngữ tự nhiên dựa trên tập luật)** gồm 5 giai đoạn:

#### 1. Kiểm tra đầu vào và bảo mật
- Kiểm tra method `POST`.
- Đọc nội dung `message` từ request body JSON hoặc form data.
- Giới hạn độ dài tin nhắn tối đa 300 ký tự (tránh spam / làm nghẽn server).

#### 2. Chuẩn hóa tiếng Việt không dấu (`removeAccents`)
Người dùng có thể gõ có dấu hoặc không dấu (`iphone dưới 20 triệu` hoặc `iphone duoi 20 trieu`). Hàm chuẩn hóa sẽ chuyển toàn bộ về chữ thường và thay thế các nguyên âm tiếng Việt thành chữ không dấu:
```php
function removeAccents($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $chars = [
        'a' => ['à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ'],
        'e' => ['è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ'],
        'i' => ['ì','í','ị','ỉ','ĩ'],
        'o' => ['ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ'],
        'u' => ['ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ'],
        'y' => ['ỳ','ý','ỵ','ỷ','ỹ'],
        'd' => ['đ'],
    ];
    foreach ($chars as $non => $accs) {
        $str = str_replace($accs, $non, $str);
    }
    return $str;
}
```

#### 3. Trích xuất mức giá / ngân sách (`extractBudget`)
Hàm sử dụng Regular Expression (Regex) để nhận diện các dạng số tiền:
- Dạng triệu: `15 triệu`, `15tr`, `15m` -> Nhân với `1.000.000`.
- Dạng nghìn: `500k`, `500 nghin`, `500 ngàn` -> Nhân với `1.000`.
- Dạng số đầy đủ: `15000000`, `15.000.000`, `15,000,000`.
- Nhận diện từ khóa so sánh: `dưới`, `<`, `<=`, `tầm`, `khoảng`, `trên`, `>`.
- Trả về mảng `['min' => ..., 'max' => ...]`.

#### 4. Truy vấn sản phẩm an toàn với Prepared Statement (`queryProducts`)
Để đảm bảo an toàn tuyệt đối chống **SQL Injection**, câu lệnh SQL dùng dấu hỏi `?` và hàm `bind_param`:
```php
function queryProducts($conn, $keyword = null, $categoryId = null, $budget = null, $limit = 4) {
    $sql = "SELECT p.id, p.name, p.price, p.original_price, p.quantity, p.image, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE 1=1";
    // Thêm điều kiện category_id, name/description LIKE ?, price <= ?
    // ...
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
```

#### 5. Phân loại theo 6 nhóm kịch bản
1. **Kiểm tra thông tin Vận chuyển / Bảo hành:**
   - Nếu khách hỏi chính sách vận chuyển, bảo hành, đổi trả: Chatbot thông báo chưa có thông tin chính thức, hướng dẫn liên hệ Hotline `0987.654.321` hoặc Email `support@shopcuatoi.vn` (không tự suy đoán).
2. **Nhóm 1: Chào hỏi & giới thiệu:**
   - Từ khóa: `xin chào`, `hello`, `shop ơi`, `bạn là ai`...
   - Phản hồi: Giới thiệu Tech Assistant + gợi ý các chủ đề.
3. **Nhóm 6: Thông tin liên hệ cửa hàng:**
   - Từ khóa: `địa chỉ`, `ở đâu`, `số điện thoại`, `hotline`, `email`, `giờ mở cửa`...
   - Phản hồi: Thông tin chính thức từ footer website.
4. **Nhóm 4: Hướng dẫn mua hàng & giỏ hàng:**
   - Từ khóa: `thêm vào giỏ`, `xem giỏ hàng`, `thay đổi số lượng`, `đặt hàng`, `thanh toán`...
   - Phản hồi: Hướng dẫn chi tiết từng bước theo chức năng thực tế của web.
5. **Nhóm 5: Tính năng website:**
   - Từ khóa: `đăng ký`, `đăng nhập`, `lịch sử mua hàng`, `voucher`, `hồ sơ`...
   - Phản hồi: Hướng dẫn kèm link dẫn tới các trang tương ứng.
6. **Nhóm 2 & 3: Tìm kiếm sản phẩm & Ngân sách:**
   - Nhận diện danh mục: Laptop, Điện thoại, Tai nghe/Loa, Smartwatch, Màn hình, v.v.
   - Kết hợp khoảng giá vừa bóc tách.
   - Truy vấn database và trả về danh sách thẻ sản phẩm.
7. **Dự phòng (Fallback):**
   - Nếu không nhận diện được: "Xin lỗi, mình chưa hiểu rõ câu hỏi của bạn..." kèm 5 chủ đề gợi ý lựa chọn.

---

### BƯỚC 2: THIẾT KẾ GIAO DIỆN CSS (`FRONTEND/assets/css/chatbot.css`)

Giao diện được thiết kế hiện đại, đồng bộ phong cách **NexTech Modern Tech Store**:
- **Nút mở chat (`.chatbot-launcher`):** Cố định ở góc dưới bên phải (`bottom: 24px; right: 24px`), hình tròn 60px, màu gradient đỏ cam (`#d3351c` -> `#e85a40`), có hiệu ứng đổ bóng và chấm xanh báo trạng thái online.
- **Cửa sổ chat (`.chatbot-window`):** Rộng 380px, cao 580px, bo tròn góc 20px, shadow nổi 3D, mở ra với hiệu ứng animation mượt mà.
- **Header:** Chứa avatar robot `🤖`, tên "Tech Assistant", trạng thái "Trực tuyến" và nút đóng `X`.
- **Bong bóng tin nhắn:**
  - Tin nhắn của khách (`.is-user`): Nằm bên phải, nền gradient đỏ cam, chữ trắng.
  - Tin nhắn của bot (`.is-bot`): Nằm bên trái, có avatar robot, nền trắng viền xám nhẹ.
- **Thẻ sản phẩm trong chat (`.chatbot-product-card`):** Có ảnh sản phẩm, tên in đậm, giá tiền đỏ và nút "Xem chi tiết" dẫn thẳng tới trang `product-detail.php?id=...`.
- **Gợi ý câu hỏi nhanh (`.chatbot-chip`):** Dạng nút bấm bo tròn, khi rê chuột chuyển sang màu đỏ cam, bấm vào sẽ tự động gửi câu hỏi.
- **Hiệu ứng gõ chữ (`.chatbot-typing`):** 3 dấu chấm nảy nhấp nháy mô phỏng bot đang trả lời.
- **Responsive Mobile:** Khi màn hình `< 576px`, cửa sổ chat tự động mở rộng full màn hình (100vw, 100vh) để khách hàng thao tác dễ dàng trên điện thoại.

---

### BƯỚC 3: XỬ LÝ LOGIC JAVASCRIPT (`FRONTEND/assets/js/chatbot.js`)

File JavaScript đảm nhiệm toàn bộ trải nghiệm người dùng phía client:
1. **Khởi tạo DOM tự động (`initChatbotDOM`):** Tự động render mã HTML của khung chat vào cuối thẻ `<body>`, không cần viết code HTML thủ công ở từng file PHP.
2. **Quản lý đóng / mở:** Bấm nút tròn để mở, bấm nút `X` để đóng, tự động focus vào ô nhập tin nhắn.
3. **Hiển thị lời chào lần đầu:** Khi người dùng mở chat lần đầu tiên, hiển thị ngay lời chào cùng 5 gợi ý phổ biến.
4. **Bảo mật chống XSS (`escapeHTML`):** Mọi nội dung do người dùng nhập hoặc dữ liệu từ server đều được escape các ký tự đặc biệt (`<`, `>`, `&`, `"`, `'`) trước khi hiển thị.
5. **Hỗ trợ định dạng văn bản Markdown nhẹ:**
   - Chuyển `**chữ in đậm**` thành `<strong>chữ in đậm</strong>`.
   - Chuyển `[tiêu đề](link)` thành `<a href="link">tiêu đề</a>`.
6. **Gọi API bất đồng bộ (`fetch()`):**
   - Xác định URL động theo domain chạy thực tế.
   - Hiển thị typing indicator trong lúc chờ đợi.
   - Thêm câu trả lời, render danh sách thẻ sản phẩm và các nút câu hỏi gợi ý mới.
   - Tự động cuộn xuống tin nhắn mới nhất (`scrollToBottom`).

---

### BƯỚC 4: NHÚNG CHATBOT VÀO HỆ THỐNG (`FRONTEND/includes/footer.php`)

Để chatbot tự động hoạt động trên toàn bộ các trang của website mà không phải sửa từng trang riêng lẻ, chỉ cần nhúng 2 dòng vào cuối file `FRONTEND/includes/footer.php` ngay trước thẻ `</body>`:

```html
<!-- Chatbot Tech Assistant -->
<link rel="stylesheet" href="assets/css/chatbot.css?v=1">
<script src="assets/js/chatbot.js?v=1" defer></script>
```

---

## IV. BẢNG CÂU HỎI MẪU & CÁCH KIỂM THỬ

| Nhóm chức năng | Câu hỏi thử nghiệm | Kết quả mong đợi |
|---|---|---|
| **Chào hỏi** | "Xin chào shop" / "Bạn là ai?" | Giới thiệu Tech Assistant + hiển thị danh sách chủ đề |
| **Tìm theo danh mục** | "Shop có bán laptop không?" | Trả về các mẫu Laptop thật có trong kho kèm giá và ảnh |
| **Tìm theo thương hiệu** | "Có điện thoại iPhone nào không?" | Trả về iPhone 18 Pro Max |
| **Lọc theo giá** | "Laptop dưới 25 triệu" | Trả về MacBook Air M2 (giá 23.990.000 đ) |
| **Giá không có sản phẩm** | "Điện thoại dưới 5 triệu" | Báo kho chưa có sản phẩm khớp, gợi ý chỉnh mức giá |
| **Thêm vào giỏ** | "Cách thêm sản phẩm vào giỏ hàng" | Hướng dẫn 3 bước thao tác |
| **Quy trình đặt hàng** | "Hướng dẫn đặt hàng" | Hướng dẫn 5 bước chọn sản phẩm -> thanh toán |
| **Tài khoản** | "Hướng dẫn đăng ký tài khoản" | Hướng dẫn điền form và dẫn link tới `register.php` |
| **Bảo hành / Vận chuyển** | "Chính sách bảo hành như thế nào?" | Thông báo chưa có thông tin chính thức, cung cấp hotline |
| **Địa chỉ / Liên hệ** | "Địa chỉ cửa hàng ở đâu?" | Trả về địa chỉ 123 Nguyễn Huệ Q.1, hotline, email |
| **Không hiểu câu hỏi** | "kjashdfkjahsdf" (từ vô nghĩa) | Kích hoạt câu trả lời dự phòng + 5 chủ đề gợi ý |

---

## V. CÁCH MỞ RỘNG VÀ BẢO TRÌ TRONG TƯƠNG LAI

1. **Thêm kịch bản trả lời mới:**
   - Mở file `BACKEND/api/chatbot/chat.php`.
   - Thêm điều kiện `elseif (str_contains($cleanText, 'từ_khóa_mới'))` vào chuỗi phân loại.
   - Điền câu trả lời `$reply` và danh sách `$suggestions`.
2. **Tích hợp thêm AI nâng cao (như Gemini API / OpenAI API):**
   - Trong `chat.php`, nếu các quy tắc Rule-based không khớp (nhánh fallback), thay vì trả về câu mặc định, có thể gọi một hàm `callGeminiAPI($message)` với prompt cung cấp danh sách sản phẩm cửa hàng để AI tự trả lời thông minh.
3. **Thay đổi giao diện màu sắc:**
   - Mở file `FRONTEND/assets/css/chatbot.css`.
   - Đổi mã màu chính `#d3351c` (đỏ cam) sang mã màu thương hiệu mong muốn.
