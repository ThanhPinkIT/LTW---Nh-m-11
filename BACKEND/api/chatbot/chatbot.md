# CHATBOT_IMPLEMENTATION_PLAN.md

# KẾ HOẠCH TRIỂN KHAI CHATBOT CƠ BẢN CHO WEBSITE BÁN HÀNG CÔNG NGHỆ

## 1. THÔNG TIN DỰ ÁN

Tên dự án: Website bán hàng công nghệ NexTech.

Địa chỉ chạy local:

[http://localhost/LTW_Web_ban_hang-main/FRONTEND/index.php](http://localhost/LTW_Web_ban_hang-main/FRONTEND/index.php)

Công nghệ:

- PHP
- HTML
- CSS thuần
- JavaScript
- MySQL

Tên chatbot: Tech Assistant.

Loại chatbot: Rule-based Chatbot (chatbot dựa trên quy tắc và kịch bản có sẵn).

Mục tiêu: Hỗ trợ khách hàng tìm kiếm sản phẩm, tư vấn theo ngân sách, hướng dẫn mua hàng và giải đáp các câu hỏi liên quan đến chức năng hiện có của website.

## 2. NGUYÊN TẮC TRIỂN KHAI

Website hiện tại đã có các chức năng đang hoạt động. AI Agent phải bảo toàn toàn bộ hệ thống hiện có.

Không được:

- Viết lại toàn bộ website.
- Thay đổi công nghệ hoặc framework.
- Xóa chức năng cũ.
- Tự ý thay đổi database schema.
- Làm ảnh hưởng đến đăng nhập, đăng xuất, giỏ hàng và đặt hàng.
- Thay đổi tên session, biến PHP, URL và tham số GET/POST nếu không có lý do đã được xác minh.
- Tạo dữ liệu sản phẩm giả.
- Đưa thông tin kết nối database lên frontend.

## 3. CHỨC NĂNG CHATBOT

### 3.1. Giao diện

- Nút chatbot nổi ở góc dưới bên phải.
- Tiêu đề Tech Assistant.
- Avatar chatbot.
- Hiển thị lời chào khi mở lần đầu.
- Phân biệt tin nhắn khách hàng và chatbot.
- Ô nhập nội dung và nút gửi.
- Các nút câu hỏi gợi ý.
- Nút đóng và mở chatbot.
- Hiển thị trạng thái đang xử lý.
- Thông báo lỗi thân thiện.
- Responsive trên desktop và mobile.

### 3.2. Nhóm câu hỏi hỗ trợ

**Nhóm 1: Chào hỏi**

- Xin chào.
- Hello.
- Shop ơi.
- Bạn là ai?

**Nhóm 2: Tìm kiếm sản phẩm**

- Shop có bán laptop không?
- Shop có điện thoại không?
- Shop có tai nghe không?
- Tìm sản phẩm theo tên.
- Tìm sản phẩm theo danh mục.

**Nhóm 3: Tư vấn theo ngân sách**

- Laptop dưới 15 triệu.
- Điện thoại dưới 10 triệu.
- Tìm sản phẩm trong khoảng giá khách nhập.

Chỉ sử dụng dữ liệu sản phẩm thật từ MySQL.

**Nhóm 4: Hướng dẫn mua hàng**

- Cách thêm sản phẩm vào giỏ hàng.
- Cách xem giỏ hàng.
- Cách thay đổi số lượng sản phẩm.
- Hướng dẫn đặt hàng theo quy trình hiện có.

**Nhóm 5: Chức năng website**

- Hướng dẫn đăng ký tài khoản.
- Hướng dẫn đăng nhập.
- Hướng dẫn xem lịch sử mua hàng nếu chức năng đã có.
- Hướng dẫn sử dụng các chức năng khách hàng hiện có.

**Nhóm 6: Liên hệ**

- Hướng dẫn khách liên hệ cửa hàng bằng thông tin chính thức đã có trong dự án.

### 3.3. Những nội dung KHÔNG triển khai

Chatbot không được cung cấp hoặc tự tạo:

- Chính sách vận chuyển.
- Chính sách bảo hành.
- Thời gian giao hàng.
- Phí vận chuyển.
- Thời hạn bảo hành.
- Chính sách đổi trả nếu website chưa có chức năng hoặc thông tin chính thức.

Nếu khách hỏi về các nội dung trên, chatbot cần phản hồi:

"Hiện tại mình chưa có thông tin chính thức về nội dung này. Bạn vui lòng liên hệ cửa hàng để được hỗ trợ nhé!"

Không được tự suy đoán hoặc tạo ra câu trả lời.

### 3.4. Câu trả lời dự phòng

Khi không nhận diện được câu hỏi:

"Xin lỗi, mình chưa hiểu rõ câu hỏi của bạn. Bạn có thể chọn một trong các chủ đề sau để mình hỗ trợ nhé!"

Hiển thị các lựa chọn:

- Tìm kiếm sản phẩm.
- Tư vấn theo ngân sách.
- Hướng dẫn mua hàng.
- Hướng dẫn sử dụng website.
- Liên hệ cửa hàng.

## 4. KẾT NỐI DATABASE

Chatbot sử dụng MySQL hiện tại để truy vấn sản phẩm.

Các thông tin cần lấy tùy theo database thực tế:

- ID sản phẩm.
- Tên sản phẩm.
- Giá bán.
- Hình ảnh.
- Danh mục.
- Mô tả.
- Tồn kho.

Chức năng:

- Tìm kiếm theo tên.
- Tìm kiếm theo danh mục.
- Lọc theo khoảng giá.
- Kiểm tra tồn kho nếu có dữ liệu.
- Hiển thị sản phẩm kèm đường dẫn chi tiết.

Yêu cầu:

- Không tự giả định tên bảng và tên cột.
- Không tạo sản phẩm giả.
- Không hiển thị sản phẩm đã bị ẩn hoặc ngừng kinh doanh nếu hệ thống có trạng thái này.
- Sử dụng prepared statements.
- Giới hạn số lượng sản phẩm trả về.

## 5. KIẾN TRÚC

Frontend:

- HTML.
- CSS.
- JavaScript.
- Fetch API.

Backend:

- PHP.
- Sử dụng kết nối database hiện tại.
- Xử lý quy tắc và phân loại câu hỏi.
- Trả dữ liệu JSON.

Database:

- Sử dụng MySQL hiện tại.
- Không tạo lại database.
- Không tự ý thay đổi các bảng đang hoạt động.

## 6. CÁC GIAI ĐOẠN THỰC HIỆN

### Giai đoạn 1: Khảo sát

- Đọc cấu trúc dự án.
- Xác định file kết nối MySQL.
- Xác định bảng sản phẩm và danh mục.
- Xác định trang chi tiết sản phẩm.
- Kiểm tra header, footer, session và JavaScript.
- Báo cáo các file dự kiến thêm và sửa.
- Chưa chỉnh sửa code khi chưa hoàn thành khảo sát.

### Giai đoạn 2: Xây dựng giao diện

- Tạo nút chatbot nổi.
- Tạo cửa sổ chat.
- Tạo khu vực tin nhắn.
- Tạo ô nhập và nút gửi.
- Tạo câu hỏi gợi ý.
- Thiết kế responsive.
- Đồng bộ giao diện Modern Tech Store.

### Giai đoạn 3: Xây dựng PHP Handler

- Nhận tin nhắn từ frontend.
- Kiểm tra phương thức HTTP.
- Kiểm tra dữ liệu đầu vào.
- Chuẩn hóa tiếng Việt.
- Phân loại câu hỏi theo nhóm.
- Xử lý câu trả lời theo kịch bản.
- Trả dữ liệu JSON.

### Giai đoạn 4: Kết nối MySQL

- Tìm kiếm sản phẩm.
- Tìm theo danh mục.
- Lọc theo ngân sách.
- Kiểm tra tồn kho.
- Trả tên, giá, hình ảnh và link chi tiết sản phẩm.

### Giai đoạn 5: Hoàn thiện kịch bản

- Bổ sung các câu hỏi thường gặp về chức năng hiện có.
- Xử lý các cách diễn đạt tương đương.
- Xử lý câu hỏi không nhận diện được.
- Xử lý câu hỏi về vận chuyển và bảo hành bằng thông báo chưa có thông tin chính thức.
- Không tạo chính sách giả.

### Giai đoạn 6: Kiểm thử

Kiểm thử tối thiểu:

1. Mở và đóng chatbot.
2. Gửi tin nhắn.
3. Chào hỏi.
4. Tìm kiếm laptop.
5. Tìm theo danh mục.
6. Lọc theo ngân sách.
7. Không tìm thấy sản phẩm.
8. Hỏi cách thêm vào giỏ hàng.
9. Hỏi cách đặt hàng.
10. Hỏi về đăng nhập và đăng ký.
11. Hỏi về bảo hành.
12. Hỏi về vận chuyển.
13. Nhập câu hỏi không nhận diện được.
14. Kiểm tra khi PHP API gặp lỗi.
15. Kiểm tra giao diện mobile.
16. Kiểm tra toàn bộ chức năng website cũ.

## 7. BẢO MẬT

- Sử dụng prepared statements.
- Kiểm tra và giới hạn dữ liệu đầu vào.
- Escape nội dung trước khi hiển thị HTML.
- Không sử dụng innerHTML trực tiếp với nội dung không đáng tin cậy.
- Giới hạn độ dài tin nhắn.
- Không cho phép truy vấn SQL trực tiếp từ nội dung khách nhập.
- Không để lộ thông tin database.
- Không cho phép truy cập trái phép vào thông tin tài khoản hoặc đơn hàng.

## 8. TIÊU CHÍ HOÀN THÀNH

- Chatbot có giao diện hoạt động.
- Gửi và nhận tin nhắn không cần tải lại trang.
- Nhận diện được các nhóm câu hỏi.
- Tìm kiếm sản phẩm thật từ MySQL.
- Lọc sản phẩm theo ngân sách.
- Hướng dẫn mua hàng dựa trên chức năng thực tế.
- Không tự tạo chính sách vận chuyển hoặc bảo hành.
- Có câu trả lời dự phòng.
- Responsive trên desktop và mobile.
- Không làm hỏng chức năng cũ.
- Không phát sinh lỗi PHP hoặc JavaScript mới.

## 9. BÁO CÁO SAU TRIỂN KHAI

AI Agent cần báo cáo:

- File mới đã tạo.
- File cũ đã chỉnh sửa.
- Các chức năng đã hoàn thành.
- Các chức năng chưa hoàn thành.
- Kết quả kiểm thử.
- Lỗi còn tồn tại và cách khắc phục.

## 10. QUY TẮC LÀM VIỆC

Thực hiện theo thứ tự:

1. Khảo sát dự án.
2. Báo cáo và chờ xác nhận.
3. Xây dựng giao diện.
4. Xây dựng PHP Handler.
5. Kết nối MySQL.
6. Hoàn thiện kịch bản.
7. Kiểm thử toàn hệ thống.

Sau mỗi giai đoạn phải báo cáo kết quả và chờ xác nhận trước khi tiếp tục.

Ưu tiên bảo toàn chức năng website hiện tại hơn việc thay đổi cấu trúc mã nguồn.
