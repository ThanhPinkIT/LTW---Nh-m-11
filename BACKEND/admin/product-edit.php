<?php
ob_start(); // cho phép redirect sau khi header.php đã in HTML
include 'header.php';
require_once 'specs-helper.php';
require_once 'images-helper.php';

if(!isset($_GET['id'])){
    header("Location: product-list.php");
    exit();
}

$id=intval($_GET['id']);

$stmt=$conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

$product=$stmt->get_result()->fetch_assoc();

if(!$product){
    die("Không tìm thấy sản phẩm.");
}

$categories=$conn->query("SELECT * FROM categories ORDER BY name");

$message="";

if(isset($_POST['update_product'])){

    $name=trim($_POST['name']);
    $price=floatval($_POST['price']);
    $category=intval($_POST['category_id']);
    $description=trim($_POST['description']);

    $image=$product['image'];

    if(isset($_FILES['image']) && $_FILES['image']['error']==0){

        if($image!="" && file_exists("../uploads/".$image)){
            unlink("../uploads/".$image);
        }
        xoa_anh_fe($image);

        $ext=pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);

        $image=time().".".$ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/".$image
        );
        copy_anh_sang_fe($image);
    }

    $stmt=$conn->prepare("
        UPDATE products
        SET
        category_id=?,
        name=?,
        price=?,
        description=?,
        image=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "isdssi",
        $category,
        $name,
        $price,
        $description,
        $image,
        $id
    );

    if($stmt->execute()){

        xoa_anh_phu($conn, $id);
        luu_anh_phu($conn, $id);

        if(luu_thong_so($conn, $id)){
            header("Location: product-list.php");
            exit();
        }

        $message="<div class='alert alert-warning'>
        Đã lưu sản phẩm nhưng chưa lưu được thông số (kiểm tra đã tạo bảng product_specs chưa).
        </div>";

    }else{

        $message="<div class='alert alert-danger'>
        ".$conn->error."
        </div>";

    }

}
?>

<h2 class="mb-4">Chỉnh sửa sản phẩm</h2>

<?= $message ?>

<div class="card shadow">
<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Tên sản phẩm</label>

<input
type="text"
name="name"
class="form-control"
value="<?= htmlspecialchars($product['name']) ?>"
required>
</div>
<div class="mb-3">
<label>Danh mục</label>
<select
name="category_id"
class="form-select">
<?php while($c=$categories->fetch_assoc()): ?>
<option
value="<?= $c['id'] ?>"
<?= $c['id']==$product['category_id']?'selected':'' ?>>
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endwhile; ?>
</select>
</div>
<div class="mb-3">
<label>Giá</label>
<input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
</div>
<div class="mb-3">
<label>Mô tả</label>
<textarea name="description" rows="6" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
</div>
<?php $specs = lay_thong_so($conn, $id); include 'specs-form.php'; ?>
<div class="mb-3">
<label>Ảnh chính hiện tại</label><br>
<img src="../uploads/<?= $product['image'] ?>" width="180" class="img-thumbnail">
</div>
<div class="mb-4">
<label>Đổi ảnh chính</label>
<input type="file" name="image" class="form-control">
</div>
<?php $anhPhu = lay_anh_phu($conn, $id); include 'images-form.php'; ?>
<button name="update_product" class="btn btn-primary"> Lưu thay đổi </button>
<a href="product-list.php" class="btn btn-secondary"> Quay lại </a>
</form>
</div>
</div>
<?php include 'footer.php'; ?>