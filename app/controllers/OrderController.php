<?php
require_once '../app/models/OrderModel.php';
require_once '../app/models/CartModel.php';

class OrderController {
    private $orderModel;
    private $cartModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->cartModel = new CartModel();
    }

    public function checkout() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $cartItems = $this->cartModel->getCart($user_id);
        if (empty($cartItems)) {
            header('Location: ?controller=cart&action=index');
            exit;
        }
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'] * (1 - $item['discount'] / 100);
        }
        $order_id = $this->orderModel->createOrder($user_id, $total);
        foreach ($cartItems as $item) {
            $this->orderModel->createOrderDetail($order_id, $item['product_id'], $item['quantity'], $item['price']);
        }
        $this->cartModel->clearCart($user_id);
        header('Location: ?controller=order&action=history');
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