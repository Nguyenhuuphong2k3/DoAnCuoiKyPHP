<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Thêm sản phẩm</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Tên sản phẩm:</label>
        <input type="text" name="name" required>
        <label>Thể loại:</label>
        <select name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Mô tả:</label>
        <textarea name="description" required></textarea>
        <label>Giá:</label>
        <input type="number" name="price" step="0.01" required>
        <label>Khuyến mãi (%):</label>
        <input type="number" name="discount" step="0.01" value="0">
        <label>Số lượng tồn kho:</label>
        <input type="number" name="stock" required>
        <label>Hình ảnh:</label>
        <input type="file" name="image" required>
        <button type="submit">Thêm</button>
    </form>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>