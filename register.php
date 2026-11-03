<?php
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/register.css?v22">

<main class="register-page">
    <div class="register-box">
        <h3 class="register-title">Đăng ký</h3>

        <div id="messageBox" class="alert d-none"></div>

        <form id="registerForm" class="register-form">
            <div class="register-field">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username"
                       name="username" required>
            </div>

            <div class="register-field">
                <label for="fullname">Họ và tên</label>
                <input type="text" id="fullname"
                       name="fullname" required>
            </div>

            <div class="register-field">
                <label for="email">Email</label>
                <input type="email" id="email"
                       name="email" required>
            </div>

            <div class="register-field">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password"
                       name="password" required>
            </div>

            <div class="register-actions">
                <button type="submit" class="register-btn">
                    Đăng ký
                </button>

                <a href="login.php" class="register-login">
                    Bạn đã có tài khoản
                </a>
            </div>
        </form>
    </div>
</main>

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
