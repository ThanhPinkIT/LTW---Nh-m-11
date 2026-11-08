<?php

include 'includes/header.php';

?>

<main class="voucher-page">

<div class="voucher-container">

<h2 class="voucher-page-title">Tất cả voucher</h2>

<section class="voucher-box">

<h3 class="voucher-heading">

<i class="bi bi-ticket-perforated"></i>

Áp dụng mã giảm giá

</h3>

<div class="voucher-input-row">

<input

type="text"

id="voucherCode"

placeholder="Nhập mã giảm giá"

autocomplete="off"

>

<button type="button" id="applyVoucherBtn"

class="apply-voucher-btn">

Áp dụng

</button>

<button type="button" id="removeVoucherBtn"

class="choose-voucher-btn" hidden>

Gỡ mã

</button>

</div>

<p id="voucherMessage" class="voucher-message"

aria-live="polite"></p>

<!-- Danh sách voucher được JS tải từ /voucher/index.php -->
<div class="voucher-list" id="voucherList">

<p class="text-muted mb-0">Đang tải voucher...</p>

</div>

<div class="voucher-summary">

<div class="voucher-summary-row">

<span>Tạm tính:</span>

<span id="subtotal">0đ</span>

</div>

<div class="voucher-summary-row">

<span>Phí vận chuyển:</span>

<span id="shippingFee">0đ</span>

</div>

<div class="voucher-summary-row voucher-discount">

<span>Giảm giá:</span>

<span id="discountAmount">0đ</span>

</div>

<div class="voucher-summary-row voucher-total">

<strong>Tổng thanh toán:</strong>

<strong id="totalAmount">0đ</strong>

</div>

</div>

<button type="button" id="voucherCheckoutBtn"

class="voucher-checkout-btn">

Tiến hành thanh toán

</button>

</section>

</div>

</main>

<link rel="stylesheet" href="assets/css/voucher.css?v=<?= filemtime(__DIR__ . '/assets/css/voucher.css') ?>">
<script src="assets/js/voucher.js?v=<?= filemtime(__DIR__ . '/assets/js/voucher.js') ?>"></script>

<?php include 'includes/footer.php'; ?>
