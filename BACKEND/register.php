<?php
include 'includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h3 class="mt-4"> Đăng ký tài khoản </h3>
        <div id="messageBox" class="alert d-none"></div>
        <form id="registerForm">
            <div class="mb-3">
                <label> Tên đăng nhập </label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label> Họ và tên </label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3"> 
                <label> Email </label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label> Mật khẩu </label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success"> Đăng ký </button>
            <a href="login.php" class="btn btn-link"> Đã có tài khoản? </a>
        </form>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageBox = document.getElementById('messageBox');
    messageBox.classList.add('d-none');
    
    try {
        const res = await fetchAPI('/auth/register.php', {
            method: 'POST',
            body: Object.fromEntries(formData.entries())
        });
        
        messageBox.className = 'alert alert-success';
        messageBox.textContent = res.message;
        messageBox.classList.remove('d-none');
        
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1500);
    } catch (err) {
        messageBox.className = 'alert alert-danger';
        messageBox.textContent = err.message;
        messageBox.classList.remove('d-none');
    }
});
</script>

<?php include 'includes/footer.php'; ?>