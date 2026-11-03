<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'header.php';

// ======================
// THỐNG KÊ
// ======================

// Tổng sản phẩm
$res = $conn->query("SELECT COUNT(*) AS total FROM products");
$total_products = $res->fetch_assoc()['total'];

// Tổng danh mục
$res = $conn->query("SELECT COUNT(*) AS total FROM categories");
$total_categories = $res->fetch_assoc()['total'];

// Tổng người dùng
$res = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='customer'");
$total_users = $res->fetch_assoc()['total'];

// Tổng đơn hàng
$res = $conn->query("SELECT COUNT(*) AS total FROM orders");
$total_orders = $res->fetch_assoc()['total'];

// Doanh thu
$res = $conn->query("
SELECT IFNULL(SUM(total_money),0) AS total
FROM orders
WHERE status='Completed'
");
$total_money = $res->fetch_assoc()['total'];

// Danh sách user
$result_users = $conn->query("
SELECT id,username,fullname,email,role,created_at
FROM users
ORDER BY id DESC
");

// 5 đơn hàng mới nhất
$new_orders = $conn->query("
SELECT
orders.id,
users.fullname,
orders.total_money,
orders.status,
orders.created_at
FROM orders
JOIN users
ON users.id=orders.user_id
ORDER BY orders.created_at DESC
LIMIT 5
");
?>
<h2 class="fw-bold mb-4">
📊 Dashboard
</h2>
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <div class="display-5 mb-2"> 📦 </div>
                <h6 class="text-muted"> Tổng sản phẩm </h6>
                <h2 class="fw-bold text-primary"> <?= $total_products ?> </h2>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <div class="display-5 mb-2"> 📂 </div>
                <h6 class="text-muted"> Danh mục </h6>
                <h2 class="fw-bold text-info"> <?= $total_categories ?> </h2>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <div class="display-5 mb-2"> 👤 </div>
                <h6 class="text-muted"> Khách hàng </h6>
                <h2 class="fw-bold text-success"> <?= $total_users ?> </h2>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <div class="display-5 mb-2"> 🛒 </div>
                <h6 class="text-muted"> Đơn hàng </h6>
                <h2 class="fw-bold text-warning"> <?= $total_orders ?> </h2>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 mb-4">
        <div class="card bg-success text-white shadow border-0">
            <div class="card-body text-center">
                <h5> 💰 Tổng doanh thu </h5>
                <h2 class="fw-bold"> <?= number_format($total_money,0,",",".") ?> đ </h2>
            </div>
        </div>
    </div>
</div>
<div class="card shadow border-0 mb-5">
    <div class="card-header bg-dark text-white"> 🛒 5 đơn hàng mới nhất </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
            </tr>
            </thead>
            <tbody>
            <?php if($new_orders->num_rows>0): ?>
                <?php while($row=$new_orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td class="fw-bold text-danger">
                        <?= number_format($row['total_money'],0,",",".") ?> đ </td>
                    <td>
                        <?php
                        switch($row['status']){
                            case 'Pending':
                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                                break;
                            case 'Processing':
                                echo '<span class="badge bg-info">Processing</span>';
                                break;
                            case 'Shipping':
                                echo '<span class="badge bg-primary">Shipping</span>';
                                break;
                            case 'Completed':
                                echo '<span class="badge bg-success">Completed</span>';
                                break;
                            default:
                                echo '<span class="badge bg-danger">Cancelled</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?= date("d/m/Y H:i",strtotime($row['created_at'])) ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center"> Chưa có đơn hàng. </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="card shadow border-0">
    <div class="card-header bg-dark text-white"> 👥 Danh sách người dùng </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Ngày tạo</th>
            </tr>
            </thead>
            <tbody>
            <?php while($row=$result_users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <?php
                        if($row['role']=="admin")
                            echo '<span class="badge bg-danger">Admin</span>';
                        else
                            echo '<span class="badge bg-secondary">User</span>';
                        ?>
                    </td>
                    <td> <?= date("d/m/Y",strtotime($row['created_at'])) ?> </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>