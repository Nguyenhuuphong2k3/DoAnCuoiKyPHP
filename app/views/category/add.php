<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Thêm thể loại</h2>
    <form method="POST">
        <label>Tên thể loại:</label>
        <input type="text" name="name" required>
        <button type="submit">Thêm</button>
    </form>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>