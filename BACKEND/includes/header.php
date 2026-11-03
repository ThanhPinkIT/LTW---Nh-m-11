<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Bán Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/api.js?v=2" defer></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"> Shop Của Tôi </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"> Trang chủ </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php"> Sản phẩm </a>
                </li>
            </ul>
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-warning fw-bold" href="cart.php"> 🛒 Giỏ hàng
                        <?php
                        $cart_count = 0;
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $qty) {
                                $cart_count += $qty;
                            }
                        }
                        if ($cart_count > 0) {
                            echo "<span class='badge bg-danger ms-1'>$cart_count</span>";
                        }
                        ?>
                    </a>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-info" href="admin/index.php"> ⚙️ Quản trị </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"> Xin chào, 
                            <strong> <?= htmlspecialchars($_SESSION['username']) ?> </strong>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php"> Lịch sử mua hàng </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm ms-2" href="logout.php"> Đăng xuất </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"> Đăng nhập </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm ms-2" href="register.php"> Đăng ký </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>