<?php
include 'header.php';

$message = "";

/* ===========================
   HÀM HỖ TRỢ
=========================== */

function alert_box($type, $text){
    return "<div class='alert alert-$type'>" . htmlspecialchars($text) . "</div>";
}

// datetime-local ("2026-01-31T23:59") -> DATETIME của MySQL, rỗng -> NULL
function parse_dt($value){
    $value = trim($value ?? '');
    if($value === '') return null;
    return str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
}

function null_if_empty($value){
    $value = trim($value ?? '');
    return $value === '' ? null : $value;
}

// Kiểm tra dữ liệu form, trả về [data, error]
function read_voucher_form(){
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $type = $_POST['type'] ?? '';
    $value = (float)($_POST['value'] ?? 0);
    $max = null_if_empty($_POST['max_discount'] ?? '');
    $min = (float)($_POST['min_order'] ?? 0);
    $limit = null_if_empty($_POST['usage_limit'] ?? '');
    $perUser = (int)($_POST['per_user_limit'] ?? 0);
    $start = parse_dt($_POST['start_date'] ?? '');
    $end = parse_dt($_POST['end_date'] ?? '');

    if(!preg_match('/^[A-Z0-9_-]{3,50}$/', $code)){
        return [null, "Mã chỉ gồm chữ HOA, số, gạch (3-50 ký tự)."];
    }
    if(!in_array($type, ['percent','fixed','freeship'], true)){
        return [null, "Loại voucher không hợp lệ."];
    }
    if($type === 'percent' && ($value <= 0 || $value > 100)){
        return [null, "Giảm theo % phải trong khoảng 1 - 100."];
    }
    if($type === 'fixed' && $value <= 0){
        return [null, "Số tiền giảm phải lớn hơn 0."];
    }
    if($type === 'freeship') $value = 0;
    if($min < 0 || $perUser < 0 || ($max !== null && (float)$max < 0) || ($limit !== null && (int)$limit < 0)){
        return [null, "Các giá trị không được âm."];
    }
    if($start && $end && $start > $end){
        return [null, "Ngày bắt đầu phải trước ngày kết thúc."];
    }

    return [[
        'code' => $code,
        'description' => null_if_empty($_POST['description'] ?? ''),
        'type' => $type,
        'value' => $value,
        'max_discount' => $max !== null ? (float)$max : null,
        'min_order' => $min,
        'usage_limit' => $limit !== null ? (int)$limit : null,
        'per_user_limit' => $perUser,
        'start_date' => $start,
        'end_date' => $end,
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ], null];
}

/* ===========================
   THÊM
=========================== */

if(isset($_POST['add'])){
    list($d, $err) = read_voucher_form();

    if($err){
        $message = alert_box('danger', $err);
    } else {
        try{
            $stmt = $conn->prepare("
                INSERT INTO vouchers
                (code,description,type,value,max_discount,min_order,usage_limit,per_user_limit,start_date,end_date,is_active)
                VALUES(?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->bind_param("sssdddiissi",
                $d['code'],$d['description'],$d['type'],$d['value'],$d['max_discount'],
                $d['min_order'],$d['usage_limit'],$d['per_user_limit'],
                $d['start_date'],$d['end_date'],$d['is_active']
            );
            $stmt->execute();
            $message = alert_box('success', 'Thêm voucher thành công.');
        }catch(Throwable $e){
            $message = alert_box('danger', 'Không thể thêm: mã voucher đã tồn tại.');
        }
    }
}

/* ===========================
   CẬP NHẬT
=========================== */

if(isset($_POST['update'])){
    $id = intval($_POST['id']);
    list($d, $err) = read_voucher_form();

    if($err){
        $message = alert_box('danger', $err);
    } else {
        try{
            $stmt = $conn->prepare("
                UPDATE vouchers SET
                code=?,description=?,type=?,value=?,max_discount=?,min_order=?,
                usage_limit=?,per_user_limit=?,start_date=?,end_date=?,is_active=?
                WHERE id=?
            ");
            $stmt->bind_param("sssdddiissii",
                $d['code'],$d['description'],$d['type'],$d['value'],$d['max_discount'],
                $d['min_order'],$d['usage_limit'],$d['per_user_limit'],
                $d['start_date'],$d['end_date'],$d['is_active'],$id
            );
            $stmt->execute();
            $message = alert_box('success', 'Cập nhật voucher thành công.');
        }catch(Throwable $e){
            $message = alert_box('danger', 'Không thể cập nhật: mã voucher đã tồn tại.');
        }
    }
}

/* ===========================
   BẬT / TẮT
=========================== */

if(isset($_GET['toggle'])){
    $id = intval($_GET['toggle']);
    $stmt = $conn->prepare("UPDATE vouchers SET is_active = 1 - is_active WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = alert_box('success', 'Đã đổi trạng thái voucher.');
}

/* ===========================
   XÓA (chỉ khi chưa ai dùng)
=========================== */

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $check = $conn->prepare("SELECT used_count FROM vouchers WHERE id=?");
    $check->bind_param("i", $id);
    $check->execute();
    $row = $check->get_result()->fetch_assoc();

    if($row && (int)$row['used_count'] > 0){
        $message = alert_box('danger', 'Voucher đã được sử dụng, không thể xóa. Hãy tắt voucher thay vì xóa.');
    } else {
        $stmt = $conn->prepare("DELETE FROM vouchers WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = alert_box('success', 'Đã xóa voucher.');
    }
}

/* ===========================
   LẤY VOUCHER ĐỂ SỬA
=========================== */

$edit = false;

if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM vouchers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

function dt_local($v){
    return $v ? date('Y-m-d\TH:i', strtotime($v)) : '';
}

$result = $conn->query("SELECT *,
    (end_date IS NOT NULL AND end_date < NOW()) AS expired
    FROM vouchers ORDER BY id DESC");

$e = $edit ?: [];
$val = function($k, $default = '') use ($e){
    return htmlspecialchars((string)($e[$k] ?? $default));
};
$curType = $e['type'] ?? 'percent';
?>

<h2 class="mb-4">🎟️ Quản lý voucher</h2>

<?= $message ?>

<div class="row">

<div class="col-lg-4">
<div class="card shadow mb-4">
<div class="card-header bg-dark text-white">
<?= $edit ? "Sửa voucher" : "Thêm voucher" ?>
</div>
<div class="card-body">

<form method="POST">

<?php if($edit): ?>
<input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
<?php endif; ?>

<div class="mb-3">
<label>Mã voucher</label>
<input type="text" name="code" class="form-control text-uppercase" required value="<?= $val('code') ?>">
</div>

<div class="mb-3">
<label>Mô tả</label>
<input type="text" name="description" class="form-control" value="<?= $val('description') ?>">
</div>

<div class="mb-3">
<label>Loại giảm</label>
<select name="type" class="form-select">
<option value="percent" <?= $curType=='percent'?'selected':'' ?>>Giảm theo %</option>
<option value="fixed" <?= $curType=='fixed'?'selected':'' ?>>Giảm số tiền cố định</option>
<option value="freeship" <?= $curType=='freeship'?'selected':'' ?>>Miễn phí vận chuyển</option>
</select>
</div>

<div class="mb-3">
<label>Giá trị (% hoặc đ — bỏ qua với freeship)</label>
<input type="number" step="1" min="0" name="value" class="form-control" value="<?= $val('value', 0) ?>">
</div>

<div class="mb-3">
<label>Giảm tối đa (đ) — để trống = không giới hạn</label>
<input type="number" step="1" min="0" name="max_discount" class="form-control" value="<?= $val('max_discount') ?>">
</div>

<div class="mb-3">
<label>Đơn hàng tối thiểu (đ)</label>
<input type="number" step="1" min="0" name="min_order" class="form-control" value="<?= $val('min_order', 0) ?>">
</div>

<div class="mb-3">
<label>Tổng lượt dùng — để trống = không giới hạn</label>
<input type="number" step="1" min="0" name="usage_limit" class="form-control" value="<?= $val('usage_limit') ?>">
</div>

<div class="mb-3">
<label>Mỗi người dùng tối đa (0 = không giới hạn)</label>
<input type="number" step="1" min="0" name="per_user_limit" class="form-control" value="<?= $val('per_user_limit', 1) ?>">
</div>

<div class="mb-3">
<label>Bắt đầu</label>
<input type="datetime-local" name="start_date" class="form-control" value="<?= dt_local($e['start_date'] ?? null) ?>">
</div>

<div class="mb-3">
<label>Kết thúc</label>
<input type="datetime-local" name="end_date" class="form-control" value="<?= dt_local($e['end_date'] ?? null) ?>">
</div>

<div class="form-check mb-3">
<input class="form-check-input" type="checkbox" name="is_active" id="is_active"
<?= (!$edit || !empty($e['is_active'])) ? 'checked' : '' ?>>
<label class="form-check-label" for="is_active">Đang hoạt động</label>
</div>

<?php if($edit): ?>
<button name="update" class="btn btn-warning w-100">Cập nhật</button>
<a href="voucher.php" class="btn btn-secondary w-100 mt-2">Hủy</a>
<?php else: ?>
<button name="add" class="btn btn-success w-100">Thêm voucher</button>
<?php endif; ?>

</form>

</div>
</div>
</div>

<div class="col-lg-8">
<div class="card shadow">
<div class="table-responsive">
<table class="table table-bordered table-hover align-middle mb-0">
<thead class="table-dark">
<tr>
<th>Mã</th>
<th>Ưu đãi</th>
<th>Điều kiện</th>
<th>Đã dùng</th>
<th>Hiệu lực</th>
<th>Trạng thái</th>
<th>Thao tác</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td>
<strong><?= htmlspecialchars($row['code']) ?></strong><br>
<small class="text-muted"><?= htmlspecialchars($row['description'] ?? '') ?></small>
</td>
<td>
<?php
if($row['type']=='percent') echo "Giảm ".(float)$row['value']."%";
elseif($row['type']=='fixed') echo "Giảm ".number_format($row['value'])." đ";
else echo "Miễn phí ship";
if($row['max_discount'] !== null) echo "<br><small>Tối đa ".number_format($row['max_discount'])." đ</small>";
?>
</td>
<td>
Đơn từ <?= number_format($row['min_order']) ?> đ<br>
<small>Mỗi người: <?= $row['per_user_limit'] > 0 ? $row['per_user_limit'].' lần' : 'không giới hạn' ?></small>
</td>
<td><?= (int)$row['used_count'] ?> / <?= $row['usage_limit'] !== null ? (int)$row['usage_limit'] : '∞' ?></td>
<td>
<small>
<?= $row['start_date'] ? date('d/m/Y H:i', strtotime($row['start_date'])) : '—' ?><br>
<?= $row['end_date'] ? date('d/m/Y H:i', strtotime($row['end_date'])) : '—' ?>
</small>
</td>
<td>
<?php if(!$row['is_active']): ?>
<span class="badge bg-secondary">Tắt</span>
<?php elseif($row['expired']): ?>
<span class="badge bg-danger">Hết hạn</span>
<?php else: ?>
<span class="badge bg-success">Hoạt động</span>
<?php endif; ?>
</td>
<td class="text-nowrap">
<a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
<a href="?toggle=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm">Bật/Tắt</a>
<a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Xóa voucher này?')">Xóa</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</div>

</div>

<?php include 'footer.php'; ?>
