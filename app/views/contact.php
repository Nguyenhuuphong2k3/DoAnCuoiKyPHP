<?php 
require_once 'layouts/header.php'; 
?>

<main>
    <h2>Gửi ý kiến đóng góp</h2>

    <?php if (isset($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="?controller=contact&action=send" method="POST" class="contact-form" enctype="multipart/form-data">
        <label for="name">Họ và tên:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email của bạn:</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Nội dung ý kiến:</label>
        <textarea id="message" name="message" rows="5" required></textarea>

        <label for="attachment">Đính kèm file (hình ảnh, PDF, DOCX, ...):</label>
        <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">

        <button type="submit">Gửi ý kiến</button>
    </form>
</main>

<?php 
require_once 'layouts/footer.php'; 
?>