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
                    <th>Tổng</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['name']; ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td><?php echo number_format($order['price']); ?> VNĐ</td>
                        <td><?php echo number_format($order['total']); ?> VNĐ</td>
                        <td><?php echo $order['status']; ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>