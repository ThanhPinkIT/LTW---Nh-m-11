<?php
include 'header.php';
require_once "../api/helper/voucher.php";
require_once "../api/helper/order.php";
ensure_order_shipping_columns($conn);
$message = "";

// Cập nhật trạng thái
if(isset($_POST['update_status'])){
    $id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si",$status,$id);

    if($stmt->execute()){
        // Đơn bị hủy -> hoàn lại lượt dùng voucher
        if($status === 'Cancelled'){
            voucher_release_usage($conn,$id);
        }
        $message = "<div class='alert alert-success'>
        Cập nhật trạng thái thành công.
        </div>";
    }

}

$search="";

$sql="
SELECT
o.*,
u.username,
u.fullname
FROM orders o
JOIN users u
ON o.user_id=u.id
";

if(isset($_GET['search']) && $_GET['search']!=""){

    $search=$conn->real_escape_string($_GET['search']);

    $sql.=" WHERE
    u.username LIKE '%$search%'
    OR u.fullname LIKE '%$search%'
    OR o.receiver_name LIKE '%$search%'
    OR o.receiver_phone LIKE '%$search%'
    OR o.id LIKE '%$search%'";

}

$sql.=" ORDER BY o.id DESC";

$result=$conn->query($sql);

$total=$result->num_rows;
?>

<div class="d-flex justify-content-between mb-4">

<h2>🧾 Quản lý đơn hàng</h2>

<form class="d-flex">

<input
class="form-control me-2"
name="search"
placeholder="Mã đơn, khách hàng hoặc SĐT"
value="<?= htmlspecialchars($search) ?>">

<button class="btn btn-primary">
Tìm
</button>

</form>

</div>

<?= $message ?>

<div class="alert admin-total-bar">

Tổng đơn hàng:
<strong><?= $total ?></strong>

</div>

<div class="card shadow">

<div class="table-responsive">

<table class="table table-hover table-bordered align-middle text-center">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Tài khoản</th>

<th>Người nhận / Địa chỉ giao</th>

<th>Ngày đặt</th>

<th>Tổng tiền</th>

<th>Thanh toán</th>

<th>Trạng thái</th>

<th>Lưu</th>

<th>Chi tiết</th>

</tr>

</thead>

<tbody>

<?php while($row=$result->fetch_assoc()): ?>

<tr>

<td>

<a href="order-detail.php?id=<?= (int)$row['id'] ?>" class="fw-bold text-decoration-none">#<?= $row['id'] ?></a>

</td>

<td class="text-start">

<strong>

<?= htmlspecialchars($row['fullname']) ?>

</strong>

<br>

<small class="text-muted">

<?= htmlspecialchars($row['username']) ?>

</small>

</td>

<td class="text-start" style="min-width:220px">

<?php if(trim($row['receiver_name'])!==''): ?>

<strong><?= htmlspecialchars($row['receiver_name']) ?></strong>

<br>

<a href="tel:<?= htmlspecialchars($row['receiver_phone']) ?>">
<?= htmlspecialchars($row['receiver_phone']) ?>
</a>

<br>

<small class="text-muted">
<?= htmlspecialchars(format_order_address($row)) ?>
</small>

<?php else: ?>

<small class="text-muted">(Đơn cũ, chưa có thông tin giao hàng)</small>

<?php endif; ?>

</td>

<td>

<?= date("d/m/Y H:i",strtotime($row['created_at'])) ?>

</td>

<td class="text-danger fw-bold">

<?= number_format($row['total_money']) ?> đ

</td>

<td>

<?= htmlspecialchars($row['payment_method']) ?>

</td>

<td>

<form method="POST">

<input
type="hidden"
name="order_id"
value="<?= $row['id'] ?>">

<select
name="status"
class="form-select">

<option
value="Pending"
<?= $row['status']=="Pending"?"selected":"" ?>>
Pending
</option>

<option
value="Shipping"
<?= $row['status']=="Shipping"?"selected":"" ?>>
Shipping
</option>

<option
value="Completed"
<?= $row['status']=="Completed"?"selected":"" ?>>
Completed
</option>

<option
value="Cancelled"
<?= $row['status']=="Cancelled"?"selected":"" ?>>
Cancelled
</option>

</select>

</td>

<td>

<button
name="update_status"
class="btn btn-success btn-sm">

Lưu

</button>

</form>

</td>

<td>

<a href="order-detail.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline-primary btn-sm">
Xem
</a>

</td>

</tr>

<?php endwhile; ?>

<?php if($total==0): ?>

<tr>

<td colspan="9">

Không có đơn hàng.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php include 'footer.php'; ?>
