<?php
require_once '../app/models/OrderModel.php';
require_once '../app/models/CartModel.php';
require_once '../app/models/ProductModel.php'; // Thêm ProductModel

class OrderController {
    private $orderModel;
    private $cartModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel(); // Khởi tạo ProductModel
    }

    public function checkout() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $cartItems = $this->cartModel->getCart($user_id);

        if (empty($cartItems)) {
            $_SESSION['error'] = "Giỏ hàng trống!";
            header('Location: ?controller=cart&action=index');
            exit;
        }

        // Kiểm tra số lượng tồn kho trước khi thanh toán
        try {
            foreach ($cartItems as $item) {
                $product = $this->productModel->getById($item['product_id']);
                if ($product['stock'] < $item['quantity']) {
                    throw new Exception("Số lượng tồn kho không đủ cho sản phẩm: " . htmlspecialchars($item['name']) . ". Hiện tại chỉ còn: " . $product['stock']);
                }
            }

            // Tính tổng tiền
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'] * (1 - $item['discount'] / 100);
            }

            // Tạo đơn hàng
            $order_id = $this->orderModel->createOrder($user_id, $total);

            // Tạo chi tiết đơn hàng và cập nhật số lượng tồn kho
            foreach ($cartItems as $item) {
                $this->orderModel->createOrderDetail($order_id, $item['product_id'], $item['quantity'], $item['price']);
                // Cập nhật số lượng tồn kho
                $this->productModel->updateStock($item['product_id'], $item['quantity']);
            }

            // Xóa giỏ hàng sau khi thanh toán
            $this->cartModel->clearCart($user_id);
            $_SESSION['success'] = "Thanh toán thành công!";
            header('Location: ?controller=order&action=history');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?controller=cart&action=index');
            exit;
        }
    }

    public function history() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $orders = $this->orderModel->getOrdersByUser($user_id);
        require_once '../app/views/order/history.php';
    }
}