<?php 
require_once __DIR__ . '/../../views/layouts/header.php'; 
?>
<main>
    <h2>Danh sách sản phẩm</h2>
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
        <div class="product-actions">
            <a href="?controller=product&action=add" class="btn btn-add-product"><i class="fas fa-plus"></i> Thêm sản phẩm mới</a>
        </div>
    <?php endif; ?>
    <div class="product-list">
        <?php if (!isset($products) || !is_array($products) || empty($products)): ?>
            <p class="error">Không có sản phẩm nào để hiển thị.</p>
        <?php else: ?>
            <?php 
            // Lấy thông tin thể loại để hiển thị
            require_once __DIR__ . '/../../../app/models/CategoryModel.php';
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getAll();
            $categoryMap = [];
            foreach ($categories as $category) {
                $categoryMap[$category['id']] = $category['name'];
            }
            ?>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <img src="/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p>Thể loại: <?= isset($categoryMap[$product['category_id']]) ? htmlspecialchars($categoryMap[$product['category_id']]) : 'Không xác định' ?></p>
                    <?php
    $originalPrice = $product['price'];
    $discount = $product['discount'];
    $finalPrice = $originalPrice - ($originalPrice * $discount / 100);
?>
<p class="product-price">
    <?php if ($discount > 0): ?>
        <span class="old-price"><?= number_format($originalPrice, 0, ',', '.') ?> VNĐ</span>
        <span class="discount-percent">-<?= $discount ?>%</span><br>
        <strong class="new-price"><?= number_format($finalPrice, 0, ',', '.') ?> VNĐ</strong>
    <?php else: ?>
        <strong class="new-price"><?= number_format($originalPrice, 0, ',', '.') ?> VNĐ</strong>
    <?php endif; ?>
</p>

                    <p>Tồn kho: <?= htmlspecialchars($product['stock']) ?></p>
                    <div class="product-buttons">
                        <a href="?controller=product&action=show&id=<?= $product['id'] ?>" class="btn btn-view">Xem chi tiết</a>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
                            <a href="?controller=product&action=edit&id=<?= $product['id'] ?>" class="btn btn-edit">Sửa</a>
                            <a href="?controller=product&action=delete&id=<?= $product['id'] ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- Phân trang -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?controller=product&action=list&page=<?= $page - 1 ?><?= isset($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : '' ?>" class="prev">Trang trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?controller=product&action=list&page=<?= $i ?><?= isset($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : '' ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?controller=product&action=list&page=<?= $page + 1 ?><?= isset($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : '' ?>" class="next">Trang sau</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>
<?php 
require_once __DIR__ . '/../../views/layouts/footer.php'; 
?>