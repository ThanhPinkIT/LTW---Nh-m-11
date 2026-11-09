
<?php
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/login.css?v=6">

<main class="login-page">
    <div class="login-box">
        <h3 class="login-title">Đăng nhập</h3>

        <div id="messageBox" class="alert d-none"></div>

        <form id="loginForm" class="login-form">
            <div class="login-field">
                <label for="username">Tên đăng nhập</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                >
            </div>

            <div class="login-field">
                <label for="password">Mật khẩu</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <div class="login-actions">
                <button type="submit" class="login-btn">
                    Đăng nhập
                </button>

                <a href="register.php" class="login-register">
                    Chưa có tài khoản?
                </a>
            </div>
        </form>
    </div>
</main>

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
                window.location.href = 'BACKEND/admin/index.php';
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
