<?php
require_once '../app/models/ReviewModel.php';

class ReviewController {
    private $reviewModel;

    public function __construct() {
        $this->reviewModel = new ReviewModel();
    }

    public function add() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user']['id'];
            $product_id = $_POST['product_id'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $comment = $_POST['comment'] ?? '';

            // Kiểm tra dữ liệu đầu vào
            if (empty($product_id) || empty($rating) || $rating < 1 || $rating > 5) {
                $_SESSION['review_message'] = "Vui lòng nhập đầy đủ thông tin và đánh giá từ 1 đến 5.";
                $_SESSION['review_success'] = false;
            } else {
                // Gửi đánh giá
                $result = $this->reviewModel->create($user_id, $product_id, $rating, $comment);
                if ($result) {
                    $_SESSION['review_message'] = "Đánh giá của bạn đã được gửi thành công!";
                    $_SESSION['review_success'] = true;
                } else {
                    $_SESSION['review_message'] = "Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.";
                    $_SESSION['review_success'] = false;
                }
            }

            // Chuyển hướng về trang chi tiết sản phẩm
            header('Location: ?controller=product&action=show&id=' . $product_id);
            exit;
        }

        // Nếu không phải POST, chuyển hướng về trang chính
        header('Location: ?controller=product&action=list');
        exit;
    }
}