<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Giỏ hàng</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (empty($cartItems)): ?>
        <p>Giỏ hàng của bạn đang trống!</p>
    <?php else: ?>
        <table>
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
                        $maxStock = isset($item['stock']) ? $item['stock'] : 0; // Kiểm tra dự phòng
                    ?>
                    <tr>
                        <td><img src="/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['price'] * (1 - $item['discount'] / 100), 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <form method="POST" action="?controller=cart&action=update">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $maxStock; ?>" required>
                                <button type="submit" class="btn btn-small btn-edit">Cập nhật</button>
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
                    <td>
                        <a href="?controller=order&action=checkout" class="btn btn-view">Thanh toán</a>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>