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
    <link rel="stylesheet" href="assets/css/style.css?v=121">
    <link rel="stylesheet" href="assets/css/mobile.css?v=1">
    <script src="assets/js/api.js?v=3" defer></script>
    <script src="assets/js/notify.js?v=2" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar shadow-sm">
<div class="container">
        <a class="navbar-brand fw-bold" href="index.php">NexTech</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Đặt form tìm kiếm ngay đầu navbar-collapse -->
            <form class="header-search ms-lg-3 my-2 my-lg-0 flex-grow-1" action="products.php" method="GET" role="search">
                <input class="form-control rounded-pill px-3 py-1 border-0" type="search" name="search"
                       value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Tìm sản phẩm..." aria-label="Tìm kiếm">
                <button type="submit" aria-label="Tìm"><i class="bi bi-search"></i></button>
            </form>

            
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"> Trang chủ </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php#mainProductGrid">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="voucher.php">Voucher</a>
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
                            <a class="nav-link text-info" href="BACKEND/admin/index.php"> ⚙️ Quản trị </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <button
                            class="nav-link dropdown-toggle border-0 bg-transparent"
                            type="button"
                            id="accountDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Xin chào,
                            <strong>
                                <?= htmlspecialchars($_SESSION['username']) ?>
                            </strong>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end"
                            aria-labelledby="accountDropdown">

                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="bi bi-person-circle me-2"></i>
                                    Thông tin cá nhân
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="history.php">
                                    <i class="bi bi-bag-check me-2"></i>
                                    Lịch sử mua hàng
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                     <li class="nav-item">
                        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 ms-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Đăng nhập
                        </button>
                    </li>
                <?php endif; ?>
    </div>
</nav>
<!-- Popup bảng thông báo khi ấn Đăng nhập -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0 shadow rounded-4 text-center p-3">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title w-100 fw-bold text-danger fs-4">Thông báo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-3">
        <div class="mb-3">
          <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="icon" style="width: 70px; height: 70px;">
        </div>
        <p class="text-secondary px-2 mb-4" style="font-size: 14.5px;">
          Vui lòng đăng nhập tài khoản để xem ưu đãi và thanh toán dễ dàng hơn.
        </p>
        <div class="d-flex justify-content-center gap-3 px-2">
          <a href="register.php" class="btn btn-outline-danger w-50 py-2 fw-semibold rounded-3">Đăng ký</a>
          <a href="login.php" class="btn btn-danger w-50 py-2 fw-semibold rounded-3">Đăng nhập</a>
        </div>
      </div>
    </div>
  </div>
</div>
