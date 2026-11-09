<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/db.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Kiểm tra quyền Admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
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
    <title>NexTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= filemtime(__DIR__ . '/../assets/css/admin.css') ?>">
</head>
<body>
<div class="admin-layout">
<aside class="admin-sidebar">
    <a class="admin-brand" href="index.php" aria-label="NexTech Admin">
        <svg viewBox="0 0 32 36" width="30" height="34" aria-hidden="true"><path d="M3 27V6l8 5v11l10 6V7l8-4v28l-8 4Z" fill="#d3351c"/><path d="m3 6 8-4 18 11-8 5Z" fill="#e85a40"/><path d="m11 11 10 7v10l-10-6Z" fill="#fff"/></svg>
        <span><strong>NexTech</strong> Admin</span>
    </a>
    <nav class="admin-menu" aria-label="Menu quản trị">
        <?php
        $menuItems = [
            ['voucher.php', 'Voucher', 'voucher', '<path d="M3 6h18v4a2 2 0 0 0 0 4v4H3v-4a2 2 0 0 0 0-4V6Z"/><path d="M15 6v12"/>'],
            ['index.php', 'Dashboard', 'dashboard', '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'],
            ['category.php', 'Danh mục', 'category', '<path d="M3 7V5a2 2 0 0 1 2-2h5l2 3h7a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/><path d="M3 9h18"/>'],
            ['product-list.php', 'Sản phẩm', 'product', '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v9l9 5 9-5V8M12 13v9M7.5 5.5l9 5"/>'],
            ['order.php', 'Đơn hàng', 'order', '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 7h6M9 11h6M9 15h4"/>'],
            ['user_list.php', 'Người dùng', 'users', '<circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6M17 15a5 5 0 0 1 4 5v1"/>'],
        ];
        $currentPage = basename($_SERVER['SCRIPT_NAME']);
        foreach ($menuItems as [$href, $label, $tone, $icon]):
            $isActive = $currentPage === $href || ($tone === 'product' && str_starts_with($currentPage, 'product-'));
        ?>
        <a href="<?= $href ?>" class="admin-menu-link<?= $isActive ? ' is-active' : '' ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
            <span class="admin-menu-icon admin-menu-icon--<?= $tone ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icon ?></svg>
            </span>
            <span><?= $label ?></span>
            <svg class="admin-menu-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-account">
        <div class="account-summary">
            <span class="admin-avatar" aria-hidden="true">A</span>
            <div><strong><?= htmlspecialchars($fullname) ?></strong><small>Quản trị viên</small></div>
        </div>
        <a class="sidebar-logout" href="../logout.php">Đăng xuất</a>
    </div>
</aside>
<div class="admin-workspace">
    <header class="admin-topbar">
        <a class="website-button" href="../../index.php">Xem Website</a>
        <div class="topbar-account dropdown">
            <button class="account-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="admin-avatar admin-avatar--small" aria-hidden="true">A</span>
                <span><?= htmlspecialchars($fullname) ?></span>
                <span class="account-caret" aria-hidden="true">⌄</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="../../profile.php">Hồ sơ</a></li>
                <li><a class="dropdown-item" href="../../index.php">Website</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="../logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </header>
    <main class="admin-content">
