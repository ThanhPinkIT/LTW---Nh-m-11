<?php
// BACKEND/api/chatbot/chat.php
// Rule-based Chatbot Handler cho NexTech - Tech Assistant

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không được hỗ trợ. Vui lòng sử dụng POST.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once "../config/database.php";

// Đọc dữ liệu đầu vào (hỗ trợ cả JSON body và URL-encoded form data)
$inputRaw = file_get_contents('php://input');
$inputData = json_decode($inputRaw, true);

if (!is_array($inputData)) {
    $inputData = $_POST;
}

$message = trim((string)($inputData['message'] ?? ''));

// Kiểm tra và giới hạn độ dài tin nhắn (tối đa 300 ký tự)
if ($message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Nội dung tin nhắn không được để trống.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

if (mb_strlen($message, 'UTF-8') > 300) {
    echo json_encode([
        'success' => false,
        'message' => 'Tin nhắn quá dài (tối đa 300 ký tự).'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// -------------------------------------------------------------
// HÀM CHUẨN HÓA TIẾNG VIỆT ĐỂ SO KHỚP TỪ KHÓA
// -------------------------------------------------------------
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

$cleanText = removeAccents($message);

// -------------------------------------------------------------
// TRÍCH XUẤT NGÂN SÁCH (SỐ TIỀN) TỪ CÂU HỎI
// -------------------------------------------------------------
function extractBudget($text) {
    // 15 triệu, 15 tr, 15trieu, 15m, 15000000, 500k, 500 ngàn, 500 nghin
    $maxPrice = null;
    $minPrice = null;

    // Pattern khoảng: từ X đến Y triệu
    if (preg_match('/(?:tu|khoang)\s*([0-9]+(?:[\.,][0-9]+)?)\s*(?:den|-)\s*([0-9]+(?:[\.,][0-9]+)?)\s*(trieu|tr|m)?/i', $text, $m)) {
        $unit = strtolower($m[3] ?? 'trieu');
        $mult = ($unit === 'k' || $unit === 'ngan' || $unit === 'nghin') ? 1000 : 1000000;
        $minVal = (float)str_replace(',', '.', $m[1]);
        $maxVal = (float)str_replace(',', '.', $m[2]);
        return ['min' => $minVal * $mult, 'max' => $maxVal * $mult];
    }

    // Pattern dưới / <= / duoi X triệu/k
    if (preg_match('/(?:duoi|<|<=|it hon|khong qua|tam|khoang)?\s*([0-9]+(?:[\.,][0-9]+)?)\s*(trieu|tr|m|k|ngan|nghin|dong|d|vnd)/i', $text, $m)) {
        $val = (float)str_replace(',', '.', $m[1]);
        $unit = strtolower($m[2]);
        $mult = 1;
        if (in_array($unit, ['trieu', 'tr', 'm'])) {
            $mult = 1000000;
        } elseif (in_array($unit, ['k', 'ngan', 'nghin'])) {
            $mult = 1000;
        }
        $price = $val * $mult;
        if (str_contains($text, 'tren') || str_contains($text, '>')) {
            return ['min' => $price, 'max' => null];
        }
        return ['min' => null, 'max' => $price];
    }

    // Pattern số nguyên lớn: 10000000, 15.000.000, 15,000,000
    if (preg_match('/(?:duoi|<|<=)?\s*([0-9]{1,3}(?:[\.,][0-9]{3})+|[0-9]{6,10})/i', $text, $m)) {
        $cleanNum = preg_replace('/[^\d]/', '', $m[1]);
        $price = (float)$cleanNum;
        if (str_contains($text, 'tren') || str_contains($text, '>')) {
            return ['min' => $price, 'max' => null];
        }
        return ['min' => null, 'max' => $price];
    }

    return null;
}

// -------------------------------------------------------------
// HÀM TRUY VẤN SẢN PHẨM TỪ MYSQL BẰNG PREPARED STATEMENT
// -------------------------------------------------------------
function queryProducts($conn, $keyword = null, $categoryId = null, $budget = null, $limit = 4) {
    if (!$conn) {
        return [];
    }
    try {
        $sql = "SELECT p.id, p.name, p.price, p.original_price, p.quantity, p.image, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];
        $types = "";

        if ($categoryId !== null) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }

        if (!empty($keyword)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $kw = "%" . $keyword . "%";
            $params[] = $kw;
            $params[] = $kw;
            $types .= "ss";
        }

        if ($budget !== null) {
            if (!empty($budget['max'])) {
                $sql .= " AND p.price <= ?";
                $params[] = $budget['max'];
                $types .= "d";
            }
            if (!empty($budget['min'])) {
                $sql .= " AND p.price >= ?";
                $params[] = $budget['min'];
                $types .= "d";
            }
        }

        $sql .= " ORDER BY p.id DESC LIMIT ?";
        $params[] = $limit;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        // Lấy tên ảnh và link chi tiết
        while ($row = $result->fetch_assoc()) {
            $imageName = basename((string)$row['image']);
            $row['imageUrl'] = $imageName ? "uploads/" . $imageName : "uploads/banner.png";
            $row['formatted_price'] = number_format((float)$row['price'], 0, ',', '.') . ' đ';
            $row['detail_url'] = "product-detail.php?id=" . (int)$row['id'];
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    } catch (\Throwable $t) {
        return [];
    }
}

// -------------------------------------------------------------
// XỬ LÝ PHÂN LOẠI CÂU HỎI (RULE-BASED INTENT CLASSIFICATION)
// -------------------------------------------------------------

$reply = "";
$suggestions = [];
$products = [];

// 1. KIỂM TRA CÂU HỎI VỀ VẬN CHUYỂN, BẢO HÀNH, ĐỔI TRẢ (MỤC 3.3 CHATBOT.MD)
$isShippingOrWarranty = (
    str_contains($cleanText, 'phi van chuyen') ||
    str_contains($cleanText, 'phi ship') ||
    str_contains($cleanText, 'bao lau nhan') ||
    str_contains($cleanText, 'khi nao nhan') ||
    str_contains($cleanText, 'giao hang') ||
    str_contains($cleanText, 'van chuyen') ||
    str_contains($cleanText, 'thoi gian giao') ||
    str_contains($cleanText, 'thoi gian ship') ||
    str_contains($cleanText, 'chinh sach doi tra') ||
    str_contains($cleanText, 'chinh sach bao hanh') ||
    str_contains($cleanText, 'bao hanh') ||
    str_contains($cleanText, 'doi tra') ||
    str_contains($cleanText, 'tra hang') ||
    str_contains($cleanText, 'hoan tien')
);

if ($isShippingOrWarranty) {
    $reply = "Hiện tại mình chưa có thông tin chính thức về nội dung này. Bạn vui lòng liên hệ cửa hàng qua hotline **0987.654.321** hoặc email **support@shopcuatoi.vn** để được hỗ trợ nhé!";
    $suggestions = [
        "📞 Hotline cửa hàng",
        "📍 Địa chỉ shop",
        "🛒 Hướng dẫn mua hàng"
    ];
}

// 2. NHÓM 1: CHÀO HỎI
elseif (
    preg_match('/^(xin chào|chào bạn|chào shop|hello|hi|shop ơi|alo|hey|ad ơi|admin ơi)/ui', $message) ||
    in_array($cleanText, ['hi', 'hello', 'alo', 'chao', 'xin chao', 'shop oi', 'ad oi']) ||
    str_contains($cleanText, 'ban la ai') ||
    str_contains($cleanText, 'ten la gi') ||
    str_contains($cleanText, 'tro ly')
) {
    $reply = "Xin chào! Mình là **Tech Assistant** - Trợ lý ảo của NexTech. Rất vui được hỗ trợ bạn!\n\nMình có thể giúp bạn:\n• Tìm kiếm thiết bị công nghệ (Laptop, Điện thoại, Tai nghe, v.v.)\n• Tư vấn sản phẩm phù hợp ngân sách\n• Hướng dẫn đặt hàng & sử dụng các tính năng website\n\nBạn cần mình hỗ trợ thông tin gì hôm nay?";
    $suggestions = [
        "📱 Tìm điện thoại",
        "💻 Tìm laptop",
        "💰 Laptop dưới 25 triệu",
        "🛒 Hướng dẫn mua hàng",
        "📞 Liên hệ cửa hàng"
    ];
}

// 3. NHÓM 6: THÔNG TIN LIÊN HỆ CỬA HÀNG
elseif (
    str_contains($cleanText, 'lien he') ||
    str_contains($cleanText, 'dia chi') ||
    str_contains($cleanText, 'o dau') ||
    str_contains($cleanText, 'so dien thoai') ||
    str_contains($cleanText, 'sdt') ||
    str_contains($cleanText, 'hotline') ||
    str_contains($cleanText, 'email') ||
    str_contains($cleanText, 'gio lam viec') ||
    str_contains($cleanText, 'gio mo cua') ||
    str_contains($cleanText, 'cua hang o dau')
) {
    $reply = "Dạ, thông tin liên hệ chính thức của **NexTech Store** như sau ạ:\n\n" .
             "📍 **Địa chỉ:** Tầng 4, Tòa Nhà Công Nghệ, 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh\n" .
             "⏰ **Giờ mở cửa:** 08:00 - 22:00 hàng ngày\n" .
             "📞 **Tư vấn mua hàng:** 0987.654.321\n" .
             "🛠 **Khiếu nại & Bảo hành:** 1900.8899\n" .
             "✉️ **Email hỗ trợ:** support@shopcuatoi.vn";
    $suggestions = [
        "🛒 Hướng dẫn đặt hàng",
        "📱 Tìm điện thoại hot",
        "🎟 Xem mã voucher"
    ];
}

// 4. NHÓM 4: HƯỚNG DẪN MUA HÀNG & GIỎ HÀNG
elseif (
    preg_match('/(them|bo|cho).*(gio|cart)/ui', $cleanText) ||
    str_contains($cleanText, 'gio hang') ||
    str_contains($cleanText, 'cach mua hang') ||
    str_contains($cleanText, 'mua hang nhu the nao') ||
    str_contains($cleanText, 'huong dan mua') ||
    str_contains($cleanText, 'cach dat hang') ||
    str_contains($cleanText, 'huong dan dat hang') ||
    str_contains($cleanText, 'quy trinh dat hang') ||
    str_contains($cleanText, 'thay doi so luong') ||
    str_contains($cleanText, 'sua gio hang') ||
    str_contains($cleanText, 'xoa khoi gio') ||
    str_contains($cleanText, 'xoa san pham') ||
    (str_contains($cleanText, 'thanh toan') && !str_contains($cleanText, 'phuong thuc')) ||
    (str_contains($cleanText, 'dat hang') && !str_contains($cleanText, 'lich su'))
) {
    if (preg_match('/(them|bo|cho).*(gio|cart)/ui', $cleanText) || str_contains($cleanText, 'them vao gio')) {
        $reply = "📌 **Cách thêm sản phẩm vào giỏ hàng:**\n\n" .
                 "1. Tại trang chủ hoặc trang sản phẩm, bấm vào nút **'Thêm vào giỏ'** ở bên dưới mỗi sản phẩm.\n" .
                 "2. Hoặc bấm vào sản phẩm để vào trang **Chi tiết sản phẩm**, chọn số lượng và bấm **'Thêm vào giỏ hàng'**.\n" .
                 "3. Số lượng sản phẩm trên biểu tượng giỏ hàng ở thanh menu sẽ tự động tăng lên ngay lập tức!";
    } elseif (preg_match('/(xem|kiem tra|mo|vao).*(gio|cart)/ui', $cleanText) || in_array($cleanText, ['gio hang', 'xem gio hang'])) {
        $reply = "📌 **Cách xem giỏ hàng:**\n\n" .
                 "Bạn chỉ cần bấm vào nút **'🛒 Giỏ hàng'** ở góc phải thanh menu trên cùng, hoặc truy cập trực tiếp đường dẫn [Giỏ hàng](cart.php) để kiểm tra các sản phẩm đã chọn.";
    } elseif (str_contains($cleanText, 'thay doi so luong') || str_contains($cleanText, 'sua gio hang') || str_contains($cleanText, 'xoa khoi gio') || str_contains($cleanText, 'xoa san pham') || str_contains($cleanText, 'so luong')) {
        $reply = "📌 **Cách thay đổi số lượng hoặc xóa sản phẩm trong giỏ:**\n\n" .
                 "1. Vào trang [Giỏ hàng](cart.php) (bấm biểu tượng giỏ hàng trên thanh menu).\n" .
                 "2. Dùng nút **'+'** hoặc **'-'** tại cột số lượng để tăng hoặc giảm số lượng mua.\n" .
                 "3. Bấm biểu tượng thùng rác màu đỏ để xóa hẳn sản phẩm ra khỏi giỏ hàng.";
    } else {
        $reply = "📌 **Quy trình đặt hàng tại NexTech:**\n\n" .
                 "1. **Chọn sản phẩm:** Tìm và thêm sản phẩm mong muốn vào giỏ hàng.\n" .
                 "2. **Kiểm tra giỏ hàng:** Vào trang [Giỏ hàng](cart.php), kiểm tra lại danh sách và nhập mã Voucher giảm giá (nếu có).\n" .
                 "3. **Tiến hành đặt hàng:** Bấm nút **'Tiến hành thanh toán'**.\n" .
                 "4. **Nhập thông tin nhận hàng:** Điền họ tên, số điện thoại, địa chỉ nhận hàng và chọn phương thức thanh toán (COD / Chuyển khoản).\n" .
                 "5. **Xác nhận đặt hàng:** Bấm **'Đặt hàng ngay'** để hoàn tất đơn hàng.";
    }
    $suggestions = [
        "🛒 Đi tới Giỏ hàng",
        "🎟 Xem voucher hiện có",
        "📦 Kiểm tra lịch sử đơn"
    ];
}

// 5. NHÓM 5: CHỨC NĂNG WEBSITE (ĐĂNG KÝ, ĐĂNG NHẬP, LỊCH SỬ ĐƠN HÀNG, VOUCHER, HỒ SƠ)
elseif (
    str_contains($cleanText, 'dang ky') ||
    str_contains($cleanText, 'tao tai khoan') ||
    str_contains($cleanText, 'dang nhap') ||
    str_contains($cleanText, 'quen mat khau') ||
    str_contains($cleanText, 'lich su mua') ||
    str_contains($cleanText, 'lich su don') ||
    str_contains($cleanText, 'tra cuu don') ||
    str_contains($cleanText, 'thong tin ca nhan') ||
    str_contains($cleanText, 'ho so') ||
    str_contains($cleanText, 'voucher') ||
    str_contains($cleanText, 'ma giam gia')
) {
    if (str_contains($cleanText, 'dang ky') || str_contains($cleanText, 'tao tai khoan')) {
        $reply = "📌 **Hướng dẫn đăng ký tài khoản:**\n\n" .
                 "1. Bấm vào mục **'Đăng ký'** trên thanh menu hoặc vào trang [Đăng ký](register.php).\n" .
                 "2. Điền đầy đủ: Tên đăng nhập, Họ và tên, Email, Mật khẩu.\n" .
                 "3. Bấm nút **'Đăng ký'** để hoàn tất. Sau khi đăng ký thành công, bạn có thể đăng nhập ngay!";
    } elseif (str_contains($cleanText, 'dang nhap')) {
        $reply = "📌 **Hướng dẫn đăng nhập:**\n\n" .
                 "1. Bấm vào mục **'Đăng nhập'** trên góc phải menu hoặc vào trang [Đăng nhập](login.php).\n" .
                 "2. Nhập Tên đăng nhập (hoặc Email) cùng Mật khẩu của bạn.\n" .
                 "3. Bấm **'Đăng nhập'**. Sau khi đăng nhập, bạn có thể xem lịch sử mua hàng và thông tin cá nhân.";
    } elseif (str_contains($cleanText, 'lich su') || str_contains($cleanText, 'tra cuu don')) {
        $reply = "📌 **Xem lịch sử mua hàng:**\n\n" .
                 "Sau khi đăng nhập tài khoản, bạn bấm vào tên của mình trên thanh menu và chọn **'Lịch sử mua hàng'** (hoặc truy cập [Lịch sử mua hàng](history.php)) để xem trạng thái các đơn hàng (Chờ xử lý, Đang giao, Hoàn thành).";
    } elseif (str_contains($cleanText, 'voucher') || str_contains($cleanText, 'ma giam gia')) {
        $reply = "🎟 **Mã giảm giá & Ưu đãi NexTech:**\n\n" .
                 "Bạn có thể vào trang [Voucher](voucher.php) để lưu các mã giảm giá hấp dẫn:\n" .
                 "• **GIAM20:** Giảm 20% tối đa 100.000đ cho mọi đơn hàng\n" .
                 "• **FREESHIP:** Miễn phí giao hàng (giảm tối đa 30.000đ)\n" .
                 "• **TECH50K:** Giảm ngay 50.000đ cho đơn từ 200.000đ\n" .
                 "Khi thanh toán tại giỏ hàng, hãy nhập mã để được áp dụng giảm giá nhé!";
    } else {
        $reply = "📌 **Quản lý tài khoản cá nhân:**\n\n" .
                 "Bạn bấm vào tên tài khoản trên thanh menu góc phải và chọn **'Thông tin cá nhân'** để xem và cập nhật họ tên, email hoặc đổi mật khẩu.";
    }
    $suggestions = [
        "🔑 Trang đăng nhập",
        "📝 Trang đăng ký",
        "🎟 Xem trang Voucher",
        "📦 Lịch sử đơn hàng"
    ];
}

// 6. NHÓM 2 & 3: TÌM KIẾM SẢN PHẨM & TƯ VẤN THEO NGÂN SÁCH
else {
    $budget = extractBudget($cleanText);
    $categoryId = null;
    $keyword = null;

    // Nhận diện danh mục
    if (str_contains($cleanText, 'laptop') || str_contains($cleanText, 'macbook')) {
        $categoryId = 1; // Laptop
    } elseif (str_contains($cleanText, 'dien thoai') || str_contains($cleanText, 'iphone') || str_contains($cleanText, 'samsung') || str_contains($cleanText, 'xiaomi')) {
        $categoryId = 2; // Điện thoại
    } elseif (str_contains($cleanText, 'tai nghe') || str_contains($cleanText, 'airpods') || str_contains($cleanText, 'loa')) {
        $keyword = str_contains($cleanText, 'tai nghe') ? 'tai nghe' : (str_contains($cleanText, 'loa') ? 'loa' : 'airpods');
    } elseif (str_contains($cleanText, 'dong ho') || str_contains($cleanText, 'watch') || str_contains($cleanText, 'smartwatch')) {
        $keyword = 'watch';
    } elseif (str_contains($cleanText, 'chuot') || str_contains($cleanText, 'ban phim') || str_contains($cleanText, 'sac') || str_contains($cleanText, 'cap') || str_contains($cleanText, 'pin')) {
        $categoryId = 3; // Phụ kiện
    } elseif (str_contains($cleanText, 'man hinh')) {
        $categoryId = 5; // Màn hình
    } elseif (str_contains($cleanText, 'ipad') || str_contains($cleanText, 'may tinh bang') || str_contains($cleanText, 'tablet')) {
        $categoryId = 6; // Máy tính bảng
    }

    // Chỉ trích xuất từ khóa tìm kiếm nếu có ý định tìm kiếm hoặc hỏi mua/giá
    $hasSearchIntent = (
        str_contains($cleanText, 'tim') ||
        str_contains($cleanText, 'co ban') ||
        str_contains($cleanText, 'ban co') ||
        str_contains($cleanText, 'muon mua') ||
        str_contains($cleanText, 'can mua') ||
        str_contains($cleanText, 'tu van') ||
        str_contains($cleanText, 'co khong') ||
        str_contains($cleanText, 'gia') ||
        str_contains($cleanText, 'bao nhieu')
    );

    if (!$categoryId && !$keyword && $hasSearchIntent) {
        $stopwords = ['shop co ban', 'shop co', 'co ban', 'ban co', 'tim san pham', 'tim', 'muon mua', 'can mua', 'tu van', 'gia bao nhieu', 'bao nhieu', 'gia', 'co khong', 'san pham'];
        $cleanKw = $cleanText;
        foreach ($stopwords as $sw) {
            $cleanKw = str_replace($sw, '', $cleanKw);
        }
        $cleanKw = trim(preg_replace('/\s+/', ' ', $cleanKw));
        if (mb_strlen($cleanKw, 'UTF-8') >= 2 && !preg_match('/^[0-9\s]+$/', $cleanKw)) {
            $keyword = $cleanKw;
        }
    }

    // Thực hiện truy vấn MySQL thật
    if ($categoryId !== null || !empty($keyword) || $budget !== null) {
        $products = queryProducts($conn, $keyword, $categoryId, $budget, 4);

        if (!empty($products)) {
            $count = count($products);
            $criteriaDesc = [];
            if ($categoryId === 1) $criteriaDesc[] = "Laptop";
            elseif ($categoryId === 2) $criteriaDesc[] = "Điện thoại";
            elseif ($categoryId === 5) $criteriaDesc[] = "Màn hình";
            elseif ($categoryId === 6) $criteriaDesc[] = "Máy tính bảng";
            elseif (!empty($keyword)) $criteriaDesc[] = "'$keyword'";

            if (!empty($budget['max'])) {
                $criteriaDesc[] = "giá dưới " . number_format($budget['max'], 0, ',', '.') . " đ";
            }
            if (!empty($budget['min'])) {
                $criteriaDesc[] = "giá từ " . number_format($budget['min'], 0, ',', '.') . " đ";
            }

            $descStr = !empty($criteriaDesc) ? implode(" ", $criteriaDesc) : "phù hợp";
            $reply = "Dạ, NexTech có các sản phẩm **$descStr** dành cho bạn đây ạ:\n(Bạn có thể bấm trực tiếp vào sản phẩm để xem chi tiết hoặc thêm vào giỏ nhé!)";
            $suggestions = [
                "🛒 Hướng dẫn mua hàng",
                "💰 Tư vấn theo mức giá khác",
                "🎟 Xem mã giảm giá"
            ];
        } else {
            // Không tìm thấy sản phẩm thật trong MySQL khớp điều kiện
            $reply = "Dạ tiếc quá, hiện tại shop chưa tìm thấy sản phẩm nào khớp với yêu cầu của bạn trong kho hàng.\n\nBạn có thể thử tìm theo danh mục khác hoặc điều chỉnh mức giá giúp mình nhé!";
            $suggestions = [
                "📱 Xem điện thoại",
                "💻 Xem laptop",
                "🎧 Xem tai nghe & loa",
                "📞 Liên hệ hỗ trợ"
            ];
        }
    } else {
        // CÂU TRẢ LỜI DỰ PHÒNG KHI KHÔNG NHẬN DIỆN ĐƯỢC CÂU HỎI (Mục 3.4)
        $reply = "Xin lỗi, mình chưa hiểu rõ câu hỏi của bạn. Bạn có thể chọn một trong các chủ đề sau để mình hỗ trợ nhé!";
        $suggestions = [
            "🔍 Tìm kiếm sản phẩm",
            "💰 Tư vấn theo ngân sách",
            "🛒 Hướng dẫn mua hàng",
            "🌐 Hướng dẫn sử dụng website",
            "📞 Liên hệ cửa hàng"
        ];
    }
}

// Trả về dữ liệu JSON đồng bộ
echo json_encode([
    'success' => true,
    'message' => 'Phản hồi thành công',
    'data' => [
        'reply' => $reply,
        'suggestions' => $suggestions,
        'products' => $products
    ]
], JSON_UNESCAPED_UNICODE);
exit();
