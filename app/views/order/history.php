<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>
<main>
    <h2>Lịch sử đơn hàng</h2>
    <?php if (empty($orders)): ?>
        <p>Bạn chưa có đơn hàng nào!</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Khuyến mãi</th>
                    <th>Tổng</th>
                    <th>Địa chỉ giao hàng</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format(is_numeric($order['price']) ? $order['price'] : 0, 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <?php 
                            $discount = isset($order['discount']) && is_numeric($order['discount']) ? $order['discount'] : 0;
                            if ($discount > 0) {
                                $discountAmount = ($order['price'] * $discount) / 100;
                                echo number_format($discountAmount, 0, ',', '.') . " VNĐ (" . $discount . "%)";
                            } else {
                                echo "Không có khuyến mãi";
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            $price = isset($order['price']) && is_numeric($order['price']) ? $order['price'] : 0;
                            $quantity = isset($order['quantity']) && is_numeric($order['quantity']) ? $order['quantity'] : 0;
                            $discountAmount = ($price * $discount) / 100;
                            $total = ($price * $quantity) - ($discountAmount * $quantity);
                            echo number_format($total, 0, ',', '.'); 
                            ?> VNĐ
                        </td>
                        <td><?php echo htmlspecialchars($order['address'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['status'] ?? 'Đang xử lý', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>