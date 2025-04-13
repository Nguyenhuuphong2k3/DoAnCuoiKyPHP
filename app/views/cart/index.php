<?php 
require_once __DIR__ . '/../../views/layouts/header.php'; 
require_once __DIR__ . '/../../models/AddressModel.php';

$addressModel = new AddressModel();
$user_id = $_SESSION['user']['id'] ?? null;
$addresses = $user_id ? $addressModel->getAddressesByUser($user_id) : [];
?>

<main class="cart-main">
    <h2>Giỏ hàng</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($cartItems) && empty($cartItems)): ?>
        <p>Giỏ hàng của bạn đang trống!</p>
    <?php elseif (isset($cartItems)): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($cartItems as $item): ?>
                    <?php 
                        $itemTotal = $item['price'] * $item['quantity'] * (1 - $item['discount'] / 100);
                        $total += $itemTotal;
                        $maxStock = $item['stock'] ?? 0;
                    ?>
                    <tr>
                        <td><img src="/images/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" class="cart-image"></td>
                        <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format($item['price'] * (1 - $item['discount'] / 100), 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <form method="POST" action="?controller=cart&action=update">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $maxStock; ?>" required class="quantity-input">
                                <button type="submit" class="btn btn-edit">Cập nhật</button>
                            </form>
                        </td>
                        <td><?php echo number_format($itemTotal, 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <a href="?controller=cart&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4"><strong>Tổng cộng:</strong></td>
                    <td><strong><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <!-- Địa chỉ giao hàng -->
        <div class="address-section">
            <h3>Chọn địa chỉ giao hàng</h3>
            <a href="?controller=order&action=confirmAddress" class="btn btn-add-address">Thêm địa chỉ mới</a>

            <?php if (!empty($addresses)): ?>
                <div class="payment-tabs">
                    <ul class="tabs-nav">
                        <li class="tab-link active" data-tab="payment-on-delivery">Thanh toán khi nhận hàng</li>
                        <li class="tab-link" data-tab="payment-momo">Thanh toán qua MoMo</li>
                    </ul>
                    
                    <!-- Form cho thanh toán khi nhận hàng -->
                    <div id="payment-on-delivery" class="tab-content active">
                        <form method="POST" action="?controller=order&action=checkout">
                            <div class="address-list">
                                <?php foreach ($addresses as $addr): ?>
                                    <label class="address-item">
                                        <input type="radio" name="address_id" value="<?php echo $addr['id']; ?>" required <?php echo $addr['is_default'] ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($addr['address'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($addr['is_default']): ?>
                                            <span class="default-address">(Địa chỉ mặc định)</span>
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="payment_method" value="cod">
                            <button type="submit" class="btn btn-checkout">Thanh toán khi nhận hàng</button>
                        </form>
                    </div>
                    
                    <!-- Form cho thanh toán MoMo -->
                    <div id="payment-momo" class="tab-content">
                        <form method="POST" action="?controller=order&action=momoPayment">
                            <div class="address-list">
                                <?php foreach ($addresses as $addr): ?>
                                    <label class="address-item">
                                        <input type="radio" name="address_id" value="<?php echo $addr['id']; ?>" required <?php echo $addr['is_default'] ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($addr['address'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($addr['is_default']): ?>
                                            <span class="default-address">(Địa chỉ mặc định)</span>
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="payment_method" value="momo">
                            <button type="submit" class="btn btn-momo">Thanh toán MoMo</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <p>Bạn chưa có địa chỉ giao hàng. Vui lòng thêm địa chỉ trước khi thanh toán.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Không thể lấy dữ liệu giỏ hàng.</p>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>

<!-- CSS + JS -->
<style>
.cart-main { max-width: 1100px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.cart-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.cart-table th, .cart-table td { padding: 12px; border-bottom: 1px solid #ddd; }
.cart-table th { background-color: #f1f1f1; }
.cart-image { width: 50px; height: 50px; object-fit: cover; }
.quantity-input { width: 60px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; }
.btn { padding: 10px 15px; border-radius: 4px; background-color: #007BFF; color: white; text-decoration: none; }
.btn:hover { background-color: #0056b3; }
.alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; color: #fff; }
.alert.success { background-color: #28a745; }
.alert.error { background-color: #dc3545; }
.address-section { margin-top: 30px; }
.address-item { display: block; margin-bottom: 10px; }
.default-address { color: green; font-weight: bold; }
.payment-tabs { margin-top: 20px; }
.tabs-nav { display: flex; padding: 0; list-style: none; margin: 0; }
.tabs-nav li { flex: 1; text-align: center; background: #f1f1f1; cursor: pointer; padding: 10px; border-radius: 4px; font-weight: bold; }
.tabs-nav li.active { background-color: #007BFF; color: white; }
.tab-content { display: none; margin-top: 20px; }
.tab-content.active { display: block; }
.btn-checkout { background-color: #28a745; }
.btn-checkout:hover { background-color: #218838; }
.btn-momo { background-color: #ff6f61; }
.btn-momo:hover { background-color: #ff4b3e; }
.btn-add-address { background-color: #ffc107; margin-bottom: 15px; display: inline-block; }
.btn-add-address:hover { background-color: #e0a800; }
</style>

<script>
document.querySelectorAll('.tab-link').forEach(tab => {
    tab.addEventListener('click', () => {
        // Cập nhật giao diện tab
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.querySelector(`#${tab.dataset.tab}`).classList.add('active');
        document.querySelectorAll('.tab-link').forEach(link => link.classList.remove('active'));
        tab.classList.add('active');
    });
});
</script>