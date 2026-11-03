<?php
include 'header.php';

$message="";

/*=========================
    XÓA SẢN PHẨM
=========================*/

if(isset($_GET['delete'])){

    $id=intval($_GET['delete']);

    $stmt=$conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();

    $img=$stmt->get_result()->fetch_assoc();

    if($img){

        if(!empty($img['image']) && file_exists("../uploads/".$img['image'])){
            unlink("../uploads/".$img['image']);
        }

        $stmt=$conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i",$id);

        if($stmt->execute()){

            $message="<div class='alert alert-success'>
            Đã xóa sản phẩm thành công.
            </div>";

        }

    }

}

/*=========================
    TÌM KIẾM
=========================*/

$key="";

$sql="
SELECT
p.*,
c.name AS category
FROM products p
LEFT JOIN categories c
ON p.category_id=c.id
";

if(isset($_GET['search']) && $_GET['search']!=""){

    $key=$conn->real_escape_string($_GET['search']);

    $sql.=" WHERE
    p.name LIKE '%$key%'
    OR c.name LIKE '%$key%'";

}

$sql.=" ORDER BY p.id DESC";

$result=$conn->query($sql);

$total=$result->num_rows;
?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>📦 Quản lý sản phẩm</h2>

<div>

<form class="d-flex">

<input
type="text"
name="search"
class="form-control me-2"
placeholder="Tên sản phẩm..."
value="<?= htmlspecialchars($key) ?>">

<button class="btn btn-primary me-2">
Tìm
</button>

<a
href="product-add.php"
class="btn btn-success">

➕ Thêm

</a>

</form>

</div>

</div>

<?= $message ?>

<div class="alert alert-info">

Tổng sản phẩm:
<strong><?= $total ?></strong>

</div>

<div class="card shadow">

<div class="table-responsive">

<table class="table table-hover table-bordered align-middle text-center">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Ảnh</th>

<th>Tên</th>

<th>Danh mục</th>

<th>Giá</th>

<th>Mô tả</th>

<th width="170">
Thao tác
</th>

</tr>

</thead>

<tbody>

<?php while($row=$result->fetch_assoc()): ?>

<tr>

<td>

<?= $row['id'] ?>

</td>

<td>

<img
src="../uploads/<?= $row['image'] ?>"
width="65"
height="65"
class="rounded border"
style="object-fit:cover;">

</td>

<td class="text-start">

<strong>

<?= htmlspecialchars($row['name']) ?>

</strong>

</td>

<td>

<span class="badge bg-secondary">

<?= htmlspecialchars($row['category'] ?? "Chưa có") ?>

</span>

</td>

<td class="text-danger fw-bold">

<?= number_format($row['price'],0,",",".") ?> đ

</td>

<td class="text-start">

<?= mb_strlen($row['description'])>80
? mb_substr(htmlspecialchars($row['description']),0,80)." ..."
: htmlspecialchars($row['description']) ?>

</td>

<td>

<a
href="product-edit.php?id=<?= $row['id'] ?>"
class="btn btn-warning btn-sm">

✏️ Sửa

</a>

<a
href="?delete=<?= $row['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Xóa sản phẩm này?')">

🗑️ Xóa

</a>

</td>

</tr>

<?php endwhile; ?>

<?php if($total==0): ?>

<tr>

<td colspan="7">

Không có sản phẩm.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php include 'footer.php'; ?>