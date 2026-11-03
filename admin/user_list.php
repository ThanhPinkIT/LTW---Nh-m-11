<?php
include 'header.php';
$key="";
$sql="SELECT * FROM users";
if(isset($_GET['search']) && $_GET['search']!=""){
    $key=$conn->real_escape_string($_GET['search']);
    $sql.=" WHERE
    username LIKE '%$key%'
    OR fullname LIKE '%$key%'
    OR email LIKE '%$key%'";
}
$sql.=" ORDER BY id DESC";
$result=$conn->query($sql);
$total=$result->num_rows;
?>
<div class="d-flex justify-content-between mb-4">
<h2>👥 Quản lý người dùng</h2>
<form class="d-flex">
<input
type="text"
name="search"
value="<?= htmlspecialchars($key) ?>"
class="form-control me-2"
placeholder="Tìm người dùng">
<button class="btn btn-primary"> Tìm </button>
</form>
</div>
<div class="alert alert-info"> Tổng người dùng: <strong><?= $total ?></strong> </div>
<div class="card shadow">
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Username</th>
<th>Họ tên</th>
<th>Email</th>
<th>Vai trò</th>
<th>Ngày tạo</th>
</tr>
</thead>
<tbody>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td>
<strong> <?= htmlspecialchars($row['username']) ?> </strong>
</td>
<td> <?= htmlspecialchars($row['fullname']) ?> </td>
<td> <?= htmlspecialchars($row['email']) ?> </td>
<td>
<?php
if($row['role']=="admin"){ echo "<span class='badge bg-danger'> Admin </span>";
}else{
echo "<span class='badge bg-success'> Customer </span>";
}
?>
</td>
<td> <?= date("d/m/Y H:i",strtotime($row['created_at'])) ?> </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
<?php include 'footer.php'; ?>