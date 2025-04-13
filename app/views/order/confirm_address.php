<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>

<style>
    .address-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        font-family: 'Segoe UI', sans-serif;
    }

    .address-container h2 {
        color: #d70018;
        font-size: 26px;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 6px;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .checkbox-group {
        margin-top: 10px;
    }

    .btn {
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        cursor: pointer;
        text-decoration: none;
        margin-top: 10px;
    }

    .btn-save {
        background-color: #28a745;
        color: white;
    }

    .btn-back {
        background-color: #6c757d;
        color: white;
        margin-bottom: 20px;
        display: inline-block;
    }

    .message {
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .success {
        background-color: #e0f7e9;
        color: #1b5e20;
    }

    .error {
        background-color: #fdecea;
        color: #c62828;
    }

    ul.address-list {
        list-style: none;
        padding: 0;
    }

    ul.address-list li {
        margin-bottom: 10px;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .default-label {
        color: #28a745;
        font-size: 13px;
        margin-left: 8px;
    }

    .address-actions a {
        margin-left: 10px;
        color: #007bff;
        text-decoration: none;
    }

    .address-actions a:hover {
        text-decoration: underline;
    }
</style>

<main class="address-container">
    <a href="http://phone_store.test/?controller=cart&action=index" class="btn btn-back">← Quay lại giỏ hàng</a>
    
    <h2>Thêm địa chỉ giao hàng</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="?controller=order&action=saveAddress">
        <div class="form-group">
            <label for="address">Địa chỉ:</label>
            <input type="text" name="address" id="address" required>
        </div>

        <div class="checkbox-group">
            <label><input type="checkbox" name="is_default" value="1"> Đặt làm địa chỉ mặc định</label>
        </div>

        <button type="submit" class="btn btn-save">Lưu địa chỉ</button>
    </form>

    <?php if (!empty($addresses)): ?>
        <h3 style="margin-top: 30px;">Danh sách địa chỉ hiện có</h3>
        <ul class="address-list">
            <?php foreach ($addresses as $addr): ?>
                <li>
                    <div>
                        <?php echo htmlspecialchars($addr['address'], ENT_QUOTES, 'UTF-8'); ?>
                        <?php if ($addr['is_default']): ?>
                            <span class="default-label">(Địa chỉ mặc định)</span>
                        <?php endif; ?>
                    </div>
                    <div class="address-actions">
                        <a href="?controller=order&action=editAddress&id=<?php echo $addr['id']; ?>">Sửa</a>
                        <a href="?controller=order&action=deleteAddress&id=<?php echo $addr['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">Xóa</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
