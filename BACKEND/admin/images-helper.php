<?php
// Ảnh phụ của sản phẩm (bảng product_images)

// ===== Đồng bộ ảnh sang FRONTEND/uploads (khỏi phải copy tay) =====
function thu_muc_anh_fe()
{
    $dir = __DIR__ . "/../../FRONTEND/uploads/";
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir;
}

// copy 1 file từ BACKEND/uploads sang FRONTEND/uploads
function copy_anh_sang_fe($filename)
{
    if ($filename == "") return;
    $src = __DIR__ . "/../uploads/" . $filename;
    if (file_exists($src)) {
        @copy($src, thu_muc_anh_fe() . $filename);
    }
}

// xóa file ảnh ở FRONTEND/uploads
function xoa_anh_fe($filename)
{
    if ($filename == "") return;
    $f = __DIR__ . "/../../FRONTEND/uploads/" . $filename;
    if (file_exists($f)) {
        @unlink($f);
    }
}

function lay_anh_phu($conn, $productId)
{
    $data = [];
    try {
        $stmt = $conn->prepare("
            SELECT id, image
            FROM product_images
            WHERE product_id=?
            ORDER BY sort_order, id
        ");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $rs = $stmt->get_result();
        while ($row = $rs->fetch_assoc()) {
            $data[] = $row;
        }
    } catch (Throwable $e) {
        // chưa tạo bảng thì trả về danh sách rỗng
    }
    return $data;
}

// xóa các ảnh phụ được tick ở form sửa (xoa_anh[] = id ảnh)
function xoa_anh_phu($conn, $productId)
{
    $ids = is_array($_POST['xoa_anh'] ?? null) ? $_POST['xoa_anh'] : [];
    foreach ($ids as $imgId) {
        $imgId = intval($imgId);
        try {
            $stmt = $conn->prepare("SELECT image FROM product_images WHERE id=? AND product_id=?");
            $stmt->bind_param("ii", $imgId, $productId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if (!$row) continue;

            $del = $conn->prepare("DELETE FROM product_images WHERE id=?");
            $del->bind_param("i", $imgId);
            $del->execute();

            $file = "../uploads/" . $row['image'];
            if ($row['image'] != "" && file_exists($file)) {
                unlink($file);
            }
            xoa_anh_fe($row['image']);
        } catch (Throwable $e) {
        }
    }
}

// lưu các ảnh phụ vừa chọn (input name="images[]"), trả về false nếu lỗi DB
function luu_anh_phu($conn, $productId)
{
    if (empty($_FILES['images']['name']) || !is_array($_FILES['images']['name'])) {
        return true;
    }

    $ok = true;
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    try {
        // số thứ tự tiếp theo
        $stmt = $conn->prepare("SELECT COALESCE(MAX(sort_order), 0) AS m FROM product_images WHERE product_id=?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $order = intval($stmt->get_result()->fetch_assoc()['m']);

        foreach ($_FILES['images']['name'] as $i => $tenGoc) {
            if ($_FILES['images']['error'][$i] != 0) continue;

            $ext = strtolower(pathinfo($tenGoc, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;

            $filename = $productId . "_" . uniqid() . "." . $ext;
            if (!move_uploaded_file($_FILES['images']['tmp_name'][$i], "../uploads/" . $filename)) continue;

            copy_anh_sang_fe($filename);
            $order++;
            $ins = $conn->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (?,?,?)");
            $ins->bind_param("isi", $productId, $filename, $order);
            $ins->execute();
        }
    } catch (Throwable $e) {
        $ok = false;
    }
    return $ok;
}
