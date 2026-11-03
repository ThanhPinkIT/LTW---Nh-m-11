<?php
include 'includes/header.php';
?>
<div class="container mt-4">
    <!-- Notification -->
    <div id="toastMessage" class="alert d-none fixed-top mt-3 mx-auto shadow" style="max-width: 400px; z-index: 9999;"></div>

    <div id="checkoutContent">
        <div class="col-12 text-center mt-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<script>
const formatVND = (price) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toastMessage');
    toast.className = `alert alert-${type} fixed-top mt-3 mx-auto shadow`;
    toast.textContent = msg;
    toast.classList.remove('d-none');
    setTimeout(() => toast.classList.add('d-none'), 3000);
}

async function loadCheckout() {
    const container = document.getElementById('checkoutContent');
    
    try {
        const res = await fetchAPI('/cart/index.php');
        const items = res.data.items;
        const total = res.data.total;
        
        if (!items || items.length === 0) {
            container.innerHTML = `
                <div class='alert alert-warning text-center mt-4'>
                    Giỏ hàng của bạn đang trống!
                    <a href='index.php'>Mua sắm ngay</a>
                </div>
            `;
            return;
        }
        
        let rows = '';
        items.forEach(item => {
            rows += `
                <tr>
                    <td>${item.name}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end text-danger fw-bold">${formatVND(item.subtotal)}</td>
                </tr>
            `;
        });
        
        container.innerHTML = `
            <div class="row">
                <div class="col-md-7">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"> Thông tin thanh toán </h4>
                        </div>
                        <div class="card-body">
                            <form id="checkoutForm">
                                <div class="mb-3">
                                    <label class="form-label"> Phương thức thanh toán </label>
                                    <select name="payment_method" class="form-select">
                                        <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                                        <option value="Banking">Chuyển khoản ngân hàng</option>
                                        <option value="Momo">Ví Momo</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg w-100 mt-3"> Xác nhận đặt hàng </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h4 class="mb-0"> Tóm tắt đơn hàng </h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                                <tfoot>
                                    <tr class="table-warning">
                                        <th colspan="2" class="text-end fs-5"> Tổng thanh toán: </th>
                                        <th class="text-end text-danger fs-5 fw-bold">${formatVND(total)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const orderRes = await fetchAPI('/order/checkout.php', {
                    method: 'POST',
                    body: Object.fromEntries(formData.entries())
                });
                
                container.innerHTML = `
                    <div class='alert alert-success text-center p-4 shadow-sm'>
                        <h3>🎉 Đặt hàng thành công!</h3>
                        <p>${orderRes.message}</p>
                        <a href='history.php' class='btn btn-primary mt-3'>Xem lịch sử đơn hàng</a>
                    </div>
                `;
            } catch (err) {
                if (err.message.includes('Auth') || err.message.includes('Login') || err.message.includes('Chưa đăng nhập')) {
                    container.innerHTML = `
                        <div class='alert alert-danger text-center mt-4'>
                            Bạn cần <a href='login.php'>đăng nhập</a> để tiến hành đặt hàng!
                        </div>
                    `;
                } else {
                    showToast(err.message, 'danger');
                }
            }
        });
        
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger">Lỗi kết nối API: ${err.message}</div>`;
    }
}

document.addEventListener('DOMContentLoaded', loadCheckout);
</script>

<?php include 'includes/footer.php'; ?>