<?php
require_once '../app/models/UserModel.php';

// Thêm require PHPMailer
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('Lỗi: Không tìm thấy file vendor/autoload.php. Vui lòng chạy lệnh "composer install" để cài đặt các thư viện.');
}
require_once $autoloadPath;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->userModel->login($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                header('Location: ?controller=default&action=index');
                exit;
            } else {
                $error = "Thông tin đăng nhập không đúng!";
            }
        }
        require_once '../app/views/user/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = 'user';
            if ($this->userModel->register($username, $email, $password, $role)) {
                header('Location: ?controller=user&action=login');
                exit;
            } else {
                $error = "Đăng ký thất bại!";
            }
        }
        require_once '../app/views/user/register.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ?controller=user&action=login');
        exit;
    }

    public function forgotPassword() {
        // Hiển thị form quên mật khẩu (đã tích hợp trong login.php)
        require_once '../app/views/user/login.php';
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';

            // Kiểm tra email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Vui lòng nhập email hợp lệ.";
                require_once '../app/views/user/login.php';
                return;
            }

            // Kiểm tra xem email có tồn tại trong hệ thống không
            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                $error = "Email không tồn tại trong hệ thống.";
                require_once '../app/views/user/login.php';
                return;
            }

            // Reset mật khẩu thành 123456
            $newPassword = '123456';
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if (!$this->userModel->updatePassword($email, $hashedPassword)) {
                $error = "Không thể reset mật khẩu. Vui lòng thử lại.";
                require_once '../app/views/user/login.php';
                return;
            }

            // Gửi email thông báo mật khẩu mới
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
                $mail->setFrom('Nguyenhuuphong2k3@gmail.com', 'Phone Store');   
                $mail->addAddress($email, $user['username']);

                // Thiết lập nội dung email
                $mail->isHTML(true);
                $mail->Subject = 'Reset mat khau tai khoan cua ban';
                $mail->Body = '<h3>Reset mật khẩu</h3>' .
                              '<p>Xin chào ' . htmlspecialchars($user['username']) . ',</p>' .
                              '<p>Chúng tôi đã reset mật khẩu của bạn theo yêu cầu.</p>' .
                              '<p>Mật khẩu mới của bạn là: <strong>' . $newPassword . '</strong></p>' .
                              '<p>Vui lòng đăng nhập và đổi mật khẩu ngay sau khi nhận được email này.</p>';

                // Gửi email
                $mail->send();
                $success = "Mật khẩu đã được reset. Vui lòng kiểm tra email của bạn.";
            } catch (Exception $e) {
                $error = "Không thể gửi email reset mật khẩu. Lỗi: {$mail->ErrorInfo}";
            }
        }

        require_once '../app/views/user/login.php';
    }
}