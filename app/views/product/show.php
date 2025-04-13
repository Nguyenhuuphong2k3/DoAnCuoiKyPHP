<?php 
require_once '../app/models/ReviewModel.php';
$reviewModel = new ReviewModel();
$reviews = $reviewModel->getByProduct($product['id']);
require_once __DIR__ . '/../../views/layouts/header.php'; 
?>
<main>
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <div class="product-detail">
        <img src="/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <div class="product-info">
        <?php
    $originalPrice = $product['price'];
    $discount = $product['discount'];
    $finalPrice = $originalPrice - ($originalPrice * $discount / 100);
?>
<p>
    <strong>Giá gốc:</strong> 
    <span class="old-price"><?= number_format($originalPrice, 0, ',', '.') ?> VNĐ</span>
</p>
<p>
    <strong>Khuyến mãi:</strong> 
    <span class="discount-percent">-<?= $discount ?>%</span>
</p>
<p>
    <strong>Giá sau khuyến mãi:</strong> 
    <span class="new-price"><?= number_format($finalPrice, 0, ',', '.') ?> VNĐ</span>
</p>

            <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
            <p><strong>Tồn kho:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
            <?php if (isset($_SESSION['user'])): ?>
                <form method="POST" action="?controller=cart&action=add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <label>Số lượng:</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button type="submit" class="btn">Thêm vào giỏ hàng</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <h3>Đánh giá sản phẩm</h3>
    <?php if (isset($_SESSION['review_message'])): ?>
        <p class="<?php echo $_SESSION['review_success'] ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($_SESSION['review_message']); ?>
        </p>
        <?php unset($_SESSION['review_message']); unset($_SESSION['review_success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['user'])): ?>
        <form method="POST" action="?controller=review&action=add">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <label>Đánh giá (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required>
            <label>Bình luận:</label>
            <textarea name="comment" required></textarea>
            <button type="submit">Gửi đánh giá</button>
        </form>
    <?php endif; ?>
    <div class="reviews">
        <?php if (empty($reviews)): ?>
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <p><strong><?php echo htmlspecialchars($review['username']); ?>:</strong> <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                    <p><small><?php echo htmlspecialchars($review['created_at']); ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>