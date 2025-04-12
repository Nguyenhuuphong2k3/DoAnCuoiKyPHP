<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Danh sách thể loại</h2>
    <a href="?controller=category&action=add" class="btn">Thêm thể loại</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo $category['id']; ?></td>
                    <td><?php echo $category['name']; ?></td>
                    <td>
                        <a href="?controller=category&action=edit&id=<?php echo $category['id']; ?>" class="btn">Sửa</a>
                        <a href="?controller=category&action=delete&id=<?php echo $category['id']; ?>" class="btn" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>