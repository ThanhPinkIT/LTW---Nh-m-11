<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/db.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Kiểm tra quyền Admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Lấy thông tin Admin
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT fullname FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$fullname = $admin['fullname'] ?? $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php"> 🛒 ADMIN PANEL </a>
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                <?= htmlspecialchars($fullname) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="../profile.php"> Hồ sơ </a>
                </li>
                <li>
                    <a class="dropdown-item" href="../index.php"> Website </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="../logout.php"> Đăng xuất </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
<div class="row">
<div class="col-md-2 bg-dark text-white min-vh-100 p-0">
    <div class="list-group rounded-0">
        <a href="index.php" class="list-group-item list-group-item-action"> 📊 Dashboard </a>
        <a href="category.php" class="list-group-item list-group-item-action"> 📂 Danh mục </a>
        <a href="product-list.php" class="list-group-item list-group-item-action"> 📦 Sản phẩm </a>
        <a href="order.php" class="list-group-item list-group-item-action"> 🧾 Đơn hàng </a>
        <a href="user_list.php" class="list-group-item list-group-item-action"> 👥 Người dùng </a>
    </div>
</div>
<div class="col-md-10 p-4">