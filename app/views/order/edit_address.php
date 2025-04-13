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
</style>

<main class="address-container">
    <a href="?controller=order&action=confirmAddress" class="btn btn-back">← Quay lại danh sách địa chỉ</a>
    
    <h2>Chỉnh sửa địa chỉ</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="?controller=order&action=updateAddress">
        <input type="hidden" name="address_id" value="<?php echo htmlspecialchars($address['id'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="address">Địa chỉ:</label>
            <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($address['address'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="checkbox-group">
            <label>
                <input type="checkbox" name="is_default" value="1" <?php echo $address['is_default'] ? 'checked' : ''; ?>> 
                Đặt làm địa chỉ mặc định
            </label>
        </div>

        <button type="submit" class="btn btn-save">Cập nhật địa chỉ</button>
    </form>
</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>