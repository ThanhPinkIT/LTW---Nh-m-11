<?php
// Đọc / lưu thông số kỹ thuật của sản phẩm (bảng product_specs)

function lay_thong_so($conn, $productId)
{
    $data = [];
    try {
        $stmt = $conn->prepare("
            SELECT spec_name, spec_value
            FROM product_specs
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

// xóa thông số cũ rồi lưu lại theo form (spec_name[] và spec_value[])
function luu_thong_so($conn, $productId)
{
    $names  = is_array($_POST['spec_name'] ?? null) ? $_POST['spec_name'] : [];
    $values = is_array($_POST['spec_value'] ?? null) ? $_POST['spec_value'] : [];

    try {
        $conn->begin_transaction();

        $del = $conn->prepare("DELETE FROM product_specs WHERE product_id=?");
        $del->bind_param("i", $productId);
        $del->execute();

        $ins = $conn->prepare("
            INSERT INTO product_specs (product_id, spec_name, spec_value, sort_order)
            VALUES (?, ?, ?, ?)
        ");

        $thuTu = 0;
        foreach ($names as $i => $name) {
            $name  = mb_substr(trim($name), 0, 100);
            $value = mb_substr(trim($values[$i] ?? ''), 0, 255);

            // bỏ qua dòng để trống
            if ($name === '' || $value === '') {
                continue;
            }

            $thuTu++;
            $ins->bind_param("issi", $productId, $name, $value, $thuTu);
            $ins->execute();
        }

        $conn->commit();
        return true;
    } catch (Throwable $e) {
        $conn->rollback();
        return false;
    }
}
