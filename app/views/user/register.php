<?php 
// Sửa đường dẫn để trỏ đúng tới thư mục layouts
require_once __DIR__ . '/../../views/layouts/header.php'; 
?>
<main>
    <h2>Đăng ký</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Mật khẩu:</label>
        <input type="password" name="password" required>
        <button type="submit">Đăng ký</button>
    </form>
</main>
<?php 
// Sửa đường dẫn cho footer
require_once __DIR__ . '/../../views/layouts/footer.php'; 
?>