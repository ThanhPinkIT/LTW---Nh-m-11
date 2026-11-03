<?php
include 'includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h3 class="mt-4"> Đăng nhập </h3>
        <div id="messageBox" class="alert d-none"></div>
        <form id="loginForm">
            <div class="mb-3">
                <label> Tên đăng nhập </label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label> Mật khẩu </label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary"> Đăng nhập </button>
            <a href="register.php" class="btn btn-link"> Chưa có tài khoản? </a>
        </form>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageBox = document.getElementById('messageBox');
    messageBox.classList.add('d-none');
    
    try {
        const res = await fetchAPI('/auth/login.php', {
            method: 'POST',
            body: Object.fromEntries(formData.entries())
        });
        
        messageBox.className = 'alert alert-success';
        messageBox.textContent = res.message;
        messageBox.classList.remove('d-none');
        
        setTimeout(() => {
            if (res.data.role === 'admin') {
                window.location.href = 'admin/index.php';
            } else {
                window.location.href = 'index.php';
            }
        }, 1000);
    } catch (err) {
        messageBox.className = 'alert alert-danger';
        messageBox.textContent = err.message;
        messageBox.classList.remove('d-none');
    }
});
</script>

<?php include 'includes/footer.php'; ?>