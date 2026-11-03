<?php
include 'header.php';

$message = "";

/* ===========================
   THÊM DANH MỤC
=========================== */

if(isset($_POST['add'])){

    $name = trim($_POST['name']);

    if($name!=""){

        $stmt=$conn->prepare("INSERT INTO categories(name) VALUES(?)");
        $stmt->bind_param("s",$name);

        if($stmt->execute()){

            $message="<div class='alert alert-success'>
            Thêm danh mục thành công.
            </div>";

        }

    }

}

/* ===========================
   CẬP NHẬT
=========================== */

if(isset($_POST['update'])){

    $id=intval($_POST['id']);
    $name=trim($_POST['name']);

    $stmt=$conn->prepare("
        UPDATE categories
        SET name=?
        WHERE id=?
    ");

    $stmt->bind_param("si",$name,$id);

    if($stmt->execute()){

        $message="<div class='alert alert-success'>
        Cập nhật thành công.
        </div>";

    }

}

/* ===========================
   XÓA
=========================== */

if(isset($_GET['delete'])){

    $id=intval($_GET['delete']);

    // Kiểm tra còn sản phẩm thuộc danh mục này không
    $check = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id=?");
    $check->bind_param("i", $id);
    $check->execute();
    $checkResult = $check->get_result()->fetch_assoc();

    if($checkResult['total'] > 0){
        $message="<div class='alert alert-danger'>
        Không thể xóa: Danh mục này còn <strong>{$checkResult['total']}</strong> sản phẩm. Hãy chuyển hoặc xóa sản phẩm trước.
        </div>";
    } else {
        $stmt=$conn->prepare("
            DELETE FROM categories
            WHERE id=?
        ");

        $stmt->bind_param("i",$id);

        if($stmt->execute()){

            $message="<div class='alert alert-success'>
            Đã xóa danh mục.
            </div>";

        }
    }

}

/* ===========================
   LẤY DANH MỤC ĐỂ SỬA
=========================== */

$edit=false;

if(isset($_GET['edit'])){

    $id=intval($_GET['edit']);

    $stmt=$conn->prepare("
        SELECT *
        FROM categories
        WHERE id=?
    ");

    $stmt->bind_param("i",$id);

    $stmt->execute();

    $edit=$stmt->get_result()->fetch_assoc();

}

/* ===========================
   TÌM KIẾM
=========================== */

$key="";

$sql="SELECT * FROM categories";

if(isset($_GET['search']) && $_GET['search']!=""){

    $key=$conn->real_escape_string($_GET['search']);

    $sql.=" WHERE name LIKE '%$key%'";

}

$sql.=" ORDER BY id DESC";

$result=$conn->query($sql);
?>

<div class="d-flex justify-content-between mb-4">

<h2>📂 Quản lý danh mục</h2>

<form class="d-flex">

<input
class="form-control me-2"
name="search"
value="<?= htmlspecialchars($key) ?>"
placeholder="Tên danh mục">

<button class="btn btn-primary">
Tìm
</button>

</form>

</div>

<?= $message ?>

<div class="row">

<div class="col-lg-4">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<?= $edit ? "Sửa danh mục" : "Thêm danh mục" ?>

</div>

<div class="card-body">

<form method="POST">

<?php if($edit): ?>

<input
type="hidden"
name="id"
value="<?= $edit['id'] ?>">

<?php endif; ?>

<div class="mb-3">

<label>Tên danh mục</label>

<input
type="text"
name="name"
class="form-control"
required
value="<?= $edit ? htmlspecialchars($edit['name']) : "" ?>">

</div>

<?php if($edit): ?>

<button
name="update"
class="btn btn-warning w-100">

Cập nhật

</button>

<a
href="category.php"
class="btn btn-secondary w-100 mt-2">

Hủy

</a>

<?php else: ?>

<button
name="add"
class="btn btn-success w-100">

Thêm danh mục

</button>

<?php endif; ?>

</form>

</div>

</div>

</div>

<div class="col-lg-8">

<div class="card shadow">

<div class="table-responsive">

<table class="table table-hover table-bordered mb-0">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Tên danh mục</th>

<th width="180">

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

<?= htmlspecialchars($row['name']) ?>

</td>

<td>

<a
href="?edit=<?= $row['id'] ?>"
class="btn btn-warning btn-sm">

Sửa

</a>

<a
href="?delete=<?= $row['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Xóa danh mục này?')">

Xóa

</a>

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