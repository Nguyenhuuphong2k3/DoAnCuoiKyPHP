<?php 
require_once 'layouts/header.php'; 
?>

<main>
    <!-- Phần Banner Quảng Cáo (Slider) -->
    <section class="banner-slider">
        <div class="slider">
            <div class="slides">
                <!-- Slide 1 -->
                <div class="slide">
                    <img src="/images/banner1.jpg" alt="Banner 1">
                    <div class="slide-content">
                        <h2>Khuyến mãi lớn!</h2>
                        <p>Giảm giá lên đến 50% cho iPhone mới nhất!</p>
                        <a href="?controller=product&action=list" class="btn btn-view">Mua ngay</a>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="slide">
                    <img src="/images/banner2.jpg" alt="Banner 2">
                    <div class="slide-content">
                        <h2>Samsung Galaxy mới!</h2>
                        <p>Trải nghiệm công nghệ đỉnh cao với giá ưu đãi!</p>
                        <a href="?controller=product&action=list" class="btn btn-view">Khám phá</a>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="slide">
                    <img src="/images/banner3.jpeg" alt="Banner 3">
                    <div class="slide-content">
                        <h2>Xiaomi 14 ra mắt!</h2>
                        <p>Hiệu năng vượt trội, giá cực sốc!</p>
                        <a href="?controller=product&action=list" class="btn btn-view">Xem ngay</a>
                    </div>
                </div>
            </div>
            <!-- Nút điều hướng slider -->
            <button class="prev-slide">❮</button>
            <button class="next-slide">❯</button>
            <!-- Dots điều hướng -->
            <div class="dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <!-- Phần Sản Phẩm Theo Thể Loại (Hãng) -->
    <section class="products-by-category">
        <div class="container">
            <h2 class="section-title">Sản phẩm theo hãng</h2>
            <?php
            require_once __DIR__ . '/../../app/models/CategoryModel.php';
            require_once __DIR__ . '/../../app/models/ProductModel.php';
            $categoryModel = new CategoryModel();
            $productModel = new ProductModel();
            $categories = $categoryModel->getAll();

            if (empty($categories)) {
                echo '<p class="error">Không có thể loại nào để hiển thị.</p>';
            } else {
                $hasProducts = false;
                foreach ($categories as $category):
                    $products = $productModel->getByCategory($category['id'], 4);
                    if (!empty($products)):
                        $hasProducts = true;
            ?>
                        <div class="category-section">
                            <h3 class="category-title"><?= htmlspecialchars($category['name']) ?></h3>
                            <div class="product-list">
                                <?php foreach ($products as $product): ?>
                                    <div class="product-item">
                                        <div class="product-img">
                                            <img src="/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="">
                                        </div>
                                        <div class="product-info">
                                            <h4><?= htmlspecialchars($product['name']) ?></h4>
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

                                            </p>
                                            <div class="product-buttons">
                                                <a href="?controller=product&action=show&id=<?= $product['id'] ?>" class="btn btn-view">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="view-more">
                                <a href="?controller=product&action=list&category_id=<?= $category['id'] ?>" class="btn btn-view">Xem thêm</a>
                            </div>
                        </div>
            <?php 
                    endif;
                endforeach; 
                if (!$hasProducts) {
                    echo '<p class="error">Hiện tại không có sản phẩm nào để hiển thị.</p>';
                }
            }
            ?>
        </div>
    </section>

    <!-- Phần Liên Hệ -->
    <section class="contact-section">
        <div class="container">
            <h2 class="section-title">Liên hệ với chúng tôi</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="contact-text">
                        <p><strong>Địa chỉ:</strong> Thủ Đức, Hồ Chí Minh</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div class="contact-text">
                        <p><strong>Hotline:</strong> 0966 595 038</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div class="contact-text">
                        <p><strong>Email:</strong> Nguyenhuuphong2k3@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php 
require_once 'layouts/footer.php'; 
?>
