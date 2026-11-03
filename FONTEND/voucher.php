
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
                    value="TECH50K"
                    placeholder="Nhập mã giảm giá"
                >
                <button type="button" id="applyVoucherBtn"
                        class="apply-voucher-btn">
                    Áp dụng
                </button>
            </div>

            <p id="voucherMessage" class="voucher-message"
               aria-live="polite"></p>

            <div class="voucher-list">
                <div class="voucher-item">
                    <div class="voucher-info">
                        <strong>Mã: GIAM20</strong>
                        <p>Giảm 20% tối đa 100.000đ cho mọi đơn hàng</p>
                    </div>
                    <button type="button"
                            class="choose-voucher-btn"
                            data-code="GIAM20">
                        Chọn
                    </button>
                </div>

                <div class="voucher-item">
                    <div class="voucher-info">
                        <strong>Mã: FREESHIP</strong>
                        <p>Miễn phí giao hàng (giảm tối đa 30.000đ)</p>
                    </div>
                    <button type="button"
                            class="choose-voucher-btn"
                            data-code="FREESHIP">
                        Chọn
                    </button>
                </div>

                <div class="voucher-item">
                    <div class="voucher-info">
                        <strong>Mã: TECH50K</strong>
                        <p>Giảm trực tiếp 50.000đ vào đơn hàng</p>
                    </div>
                    <button type="button"
                            class="choose-voucher-btn"
                            data-code="TECH50K">
                        Chọn
                    </button>
                </div>
            </div>

            <div class="voucher-summary">
                <div class="voucher-summary-row">
                    <span>Tạm tính:</span>
                    <span id="subtotal">500.000đ</span>
                </div>

                <div class="voucher-summary-row">
                    <span>Phí vận chuyển:</span>
                    <span id="shippingFee">30.000đ</span>
                </div>

                <div class="voucher-summary-row voucher-discount">
                    <span>Giảm giá:</span>
                    <span id="discountAmount">0đ</span>
                </div>

                <div class="voucher-summary-row voucher-total">
                    <strong>Tổng thanh toán:</strong>
                    <strong id="totalAmount">530.000đ</strong>
                </div>
            </div>

            <button type="button" id="voucherCheckoutBtn"
                    class="voucher-checkout-btn">
                Tiến hành thanh toán
            </button>
        </section>
    </div>
</main>

<link rel="stylesheet" href="assets/css/voucher.css?v=5">
<script src="assets/js/voucher.js?v=1"></script>

<?php include 'includes/footer.php'; ?>
