<?php
// Kiểm tra xem file autoload.php có tồn tại không
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('Lỗi: Không tìm thấy file vendor/autoload.php. Vui lòng chạy lệnh "composer install" để cài đặt các thư viện.');
}

require_once $autoloadPath;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactController {
    public function index() {
        // Hiển thị trang gửi ý kiến
        $viewPath = __DIR__ . '/../views/contact.php';
        if (!file_exists($viewPath)) {
            die('Lỗi: Không tìm thấy file views/contact.php. Vui lòng kiểm tra thư mục app/views/.');
        }
        include $viewPath;
    }

    public function send() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';

            // Kiểm tra dữ liệu đầu vào
            if (empty($name) || empty($email) || empty($message)) {
                $error = "Vui lòng điền đầy đủ thông tin.";
                $viewPath = __DIR__ . '/../views/contact.php';
                if (!file_exists($viewPath)) {
                    die('Lỗi: Không tìm thấy file views/contact.php. Vui lòng kiểm tra thư mục app/views/.');
                }
                include $viewPath;
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Email không hợp lệ.";
                $viewPath = __DIR__ . '/../views/contact.php';
                if (!file_exists($viewPath)) {
                    die('Lỗi: Không tìm thấy file views/contact.php. Vui lòng kiểm tra thư mục app/views/.');
                }
                include $viewPath;
                return;
            }

            // Xử lý file đính kèm
            $attachmentPath = null;
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['attachment'];

                // Kiểm tra lỗi tải lên
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $error = "Lỗi khi tải file lên. Vui lòng thử lại.";
                    $viewPath = __DIR__ . '/../views/contact.php';
                    if (!file_exists($viewPath)) {
                        die('Lỗi: Không tìm thấy file views/contact.php. Vui lòng kiểm tra thư mục app/views/.');
                    }
                    include $viewPath;
                    return;
                }

                // Kiểm tra kích thước file
                if ($file['size'] > $maxFileSize) {
                    $error = "File quá lớn. Kích thước tối đa là 5MB.";
                    $viewPath = __DIR__ . '/../views/contact.php';
                    if (!file_exists($viewPath)) {
                        die('Lỗi: Không tìm thấy file views/contact.php. Vui lòng kiểm tra thư mục app/views/.');
                    }
                    include $viewPath;
                    return;
                }

                // Kiểm tra loại file
                $fileType = mime_content_type($file['tmp_name']);
                if (!in_array($fileType, $allowedTypes)) {
                    $error = "Loại file không được phép. Chỉ chấp nhận hình ảnh (JPG, PNG), PDF, hoặc DOCX.";
                    $viewPath = __DIR__ . '/../views/contact.php';
                    if (!file_exists($viewPath)) {
                        die('Lỗi: Không tìm thấy file views/contact.php. Vui lòng kiểm tra thư mục app/views/.');
                    }
                    include $viewPath;
                    return;
                }

                $attachmentPath = $file['tmp_name'];
            }

            // Cấu hình PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Cấu hình server SMTP (dùng Gmail)
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'Nguyenhuuphong2k3@gmail.com'; // Thay bằng email của bạn
                $mail->Password = ''; // Thay bằng App Password của Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Thiết lập người gửi và người nhận
                $mail->setFrom($email, $name);
                $mail->addAddress('Nguyenhuuphong2k3@gmail.com', 'Phone Store');

                // Thiết lập nội dung email
                $mail->isHTML(true);
                $mail->Subject = 'Y kien dong gop tu ' . $name;
                $mail->Body = '<h3>Ý kiến đóng góp</h3>' .
                              '<p><strong>Họ và tên:</strong> ' . htmlspecialchars($name) . '</p>' .
                              '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>' .
                              '<p><strong>Nội dung:</strong> ' . nl2br(htmlspecialchars($message)) . '</p>';

                // Đính kèm file nếu có
                if ($attachmentPath) {
                    $mail->addAttachment($attachmentPath, $file['name']);
                }

                // Gửi email
                $mail->send();
                $success = "Ý kiến của bạn đã được gửi thành công!";
            } catch (Exception $e) {
                $error = "Không thể gửi ý kiến. Lỗi: {$mail->ErrorInfo}";
            }
        }

        // Hiển thị lại trang với thông báo
        $viewPath = __DIR__ . '/../views/contact.php';
        if (!file_exists($viewPath)) {
            die('Lỗi: Không tìm thấy file views/contact.php. Vui lòng kiểm tra thư mục app/views/.');
        }
        include $viewPath;
    }
}