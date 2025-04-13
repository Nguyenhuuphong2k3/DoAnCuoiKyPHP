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
        <h2>Sản phẩm theo hãng</h2>
        <?php
        // Lấy danh sách thể loại (hãng)
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
                // Lấy 4 sản phẩm mới nhất của từng thể loại
                $products = $productModel->getByCategory($category['id'], 4);
                if (!empty($products)):
                    $hasProducts = true;
        ?>
                    <div class="category-section">
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                        <div class="product-list">
                            <?php foreach ($products as $product): ?>
                                <div class="product-item">
                                    <img src="/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="">
                                    <h4><?= htmlspecialchars($product['name']) ?></h4>
                                    <p>Giá: <?= number_format($product['price'] - ($product['price'] * $product['discount'] / 100), 0, ',', '.') ?> VNĐ</p>
                                    <div class="product-buttons">
                                        <a href="?controller=product&action=show&id=<?= $product['id'] ?>" class="btn btn-view">Xem chi tiết</a>
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
    </section>

    <!-- Phần Liên Hệ -->
    <section class="contact-section">
        <h2>Liên hệ với chúng tôi</h2>
        <div class="contact-info">
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <p><strong>Địa chỉ:</strong> Linh Trung, Thủ Đức, TP. Hồ Chí Minh</p>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <p><strong>Hotline:</strong> 0966 595 038</p>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <p><strong>Email:</strong> Nguyenhuuphong2k3@gmail.com</p>
            </div>
        </div>
    </section>
</main>

<?php 
require_once 'layouts/footer.php'; 
?>