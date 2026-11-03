<?php
include 'header.php';
$message = "";
// Lấy danh mục
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
if(isset($_POST['add_product'])){
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $image = "";
    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $ext = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
        $image = time().".".$ext;
        move_uploaded_file( $_FILES['image']['tmp_name'], "../uploads/".$image );
    }
    $stmt=$conn->prepare("
        INSERT INTO products
        (category_id,name,price,description,image)
        VALUES(?,?,?,?,?)
    ");
    $stmt->bind_param(
        "isdss",
        $category,
        $name,
        $price,
        $description,
        $image
    );
    if($stmt->execute()){
        $message="<div class='alert alert-success'> Thêm sản phẩm thành công. </div>";
    }else{
        $message="<div class='alert alert-danger'> ".$conn->error." </div>";
    }
}
?>
    <h2 class="mb-4">➕ Thêm sản phẩm</h2>
<?= $message ?>
<div class="card shadow-sm">
<div class="card-body">
<form method="POST" enctype="multipart/form-data">
<div class="mb-3">
<label>Tên sản phẩm</label>
<input type="text" name="name" class="form-control" required>
</div>
<div class="mb-3">
<label>Danh mục</label>
<select name="category_id" class="form-select">
<?php while($c=$categories->fetch_assoc()): ?>
<option value="<?= $c['id'] ?>">
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endwhile; ?>
</select>
</div>
<div class="mb-3">
<label>Giá</label>
<input
type="number"
name="price"
class="form-control"
required>
</div>
<div class="mb-3">
<label>Mô tả</label>
<textarea
name="description"
rows="5"
class="form-control"></textarea>
</div>
<div class="mb-4">
<label>Ảnh</label>
<input
type="file"
name="image"
class="form-control"
required>
</div>
<button name="add_product" class="btn btn-success"> Lưu sản phẩm </button>
<a href="product-list.php" class="btn btn-secondary"> Quay lại </a>
</form>
</div>
</div>
<?php include 'footer.php'; ?>