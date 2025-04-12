<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Sửa sản phẩm</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Tên sản phẩm:</label>
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
        <label>Thể loại:</label>
        <select name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $product['category_id']) echo 'selected'; ?>>
                    <?php echo $category['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label>Mô tả:</label>
        <textarea name="description" required><?php echo $product['description']; ?></textarea>
        <label>Giá:</label>
        <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
        <label>Khuyến mãi (%):</label>
        <input type="number" name="discount" step="0.01" value="<?php echo $product['discount']; ?>">
        <label>Số lượng tồn kho:</label>
        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
        <label>Hình ảnh hiện tại:</label>
        <img src="../public/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="100">
        <label>Thay đổi hình ảnh:</label>
        <input type="file" name="image">
        <button type="submit">Cập nhật</button>
    </form>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>