<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Đăng nhập</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" required>
        <label>Mật khẩu:</label>
        <input type="password" name="password" required>
        <button type="submit">Đăng nhập</button>
    </form>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>