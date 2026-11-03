<?php
include 'includes/header.php';
?>
<div class="container mt-5">
    <div id="toastMessage" class="alert d-none fixed-top mt-3 mx-auto shadow" style="max-width: 400px; z-index: 9999;"></div>

    <div class="row justify-content-center" id="profileContent">
        <div class="col-12 text-center mt-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</div>

<script>
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toastMessage');
    toast.className = `alert alert-${type} fixed-top mt-3 mx-auto shadow`;
    toast.textContent = msg;
    toast.classList.remove('d-none');
    setTimeout(() => toast.classList.add('d-none'), 3000);
}

async function loadProfile() {
    const container = document.getElementById('profileContent');
    
    try {
        const res = await fetchAPI('/auth/profile.php');
        const user = res.data;
        
        container.innerHTML = `
        <div class="col-md-6">
            <h2 class="text-center mb-4"> 👤 Thông tin cá nhân </h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="profileForm">
                        <div class="mb-3">
                            <label class="form-label"> Username </label>
                            <input type="text" class="form-control" value="${user.username}" disabled >
                        </div>
                        <div class="mb-3">
                            <label class="form-label"> Họ và tên </label>
                            <input type="text" name="fullname" class="form-control" value="${user.fullname}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"> Email </label>
                            <input type="email" name="email" class="form-control" value="${user.email}" required>
                        </div>
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary w-100"> Lưu thay đổi </button>
                            <a href="index.php" class="btn btn-secondary w-100"> Quay lại </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;
        
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const updateRes = await fetchAPI('/auth/update-profile.php', {
                    method: 'POST',
                    body: Object.fromEntries(formData.entries())
                });
                showToast(updateRes.message, 'success');
            } catch (err) {
                showToast(err.message, 'danger');
            }
        });
        
    } catch (err) {
        if (err.message.includes('Auth') || err.message.includes('Login')) {
            window.location.href = 'login.php';
        } else {
            container.innerHTML = `<div class="alert alert-danger">Lỗi tải thông tin: ${err.message}</div>`;
        }
    }
}

document.addEventListener('DOMContentLoaded', loadProfile);
</script>

<?php include 'includes/footer.php'; ?>