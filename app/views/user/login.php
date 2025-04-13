<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Đăng nhập</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    
    <!-- Form đăng nhập -->
    <form method="POST" action="?controller=user&action=login" class="login-form">
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" required>
        <label>Mật khẩu:</label>
        <input type="password" name="password" required>
        <button type="submit">Đăng nhập</button>
    </form>

    <!-- Liên kết Quên mật khẩu -->
    <p class="forgot-password-link">
        Quên mật khẩu? <a href="?controller=user&action=forgotPassword">Nhấn vào đây</a>
    </p>

    <!-- Form Quên mật khẩu (hiển thị khi vào action forgotPassword) -->
    <?php if (isset($_GET['action']) && $_GET['action'] === 'forgotPassword'): ?>
        <h3>Quên mật khẩu</h3>
        <form method="POST" action="?controller=user&action=resetPassword" class="forgot-password-form">
            <label>Nhập email của bạn:</label>
            <input type="email" name="email" required>
            <button type="submit">Gửi yêu cầu</button>
        </form>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>