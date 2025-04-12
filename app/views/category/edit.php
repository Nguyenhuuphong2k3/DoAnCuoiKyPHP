<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Sửa thể loại</h2>
    <form method="POST">
        <label>Tên thể loại:</label>
        <input type="text" name="name" value="<?php echo $category['name']; ?>" required>
        <button type="submit">Cập nhật</button>
    </form>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>