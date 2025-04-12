<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Giỏ hàng</h2>
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
                    ?>
                    <tr>
                        <td><img src="/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" width="50"></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo number_format($item['price'] * (1 - $item['discount'] / 100)); ?> VNĐ</td>
                        <td>
                            <form method="POST" action="?controller=cart&action=update">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                <button type="submit">Cập nhật</button>
                            </form>
                        </td>
                        <td><?php echo number_format($itemTotal); ?> VNĐ</td>
                        <td>
                            <a href="?controller=cart&action=delete&id=<?php echo $item['id']; ?>" class="btn" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Tổng cộng: <?php echo number_format($total); ?> VNĐ</h3>
        <a href="?controller=order&action=checkout" class="btn">Thanh toán</a>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>