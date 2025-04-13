<?php
require_once '../app/models/CartModel.php';
require_once '../app/models/ProductModel.php';

class CartController {
    private $cartModel;
    private $productModel;

    public function __construct() {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $cartItems = $this->cartModel->getCart($user_id);
        require_once '../app/views/cart/index.php';
    }

    public function add() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        try {
            $this->cartModel->addToCart($user_id, $product_id, $quantity);
            $_SESSION['success'] = "Thêm vào giỏ hàng thành công!";
            header('Location: ?controller=cart&action=index');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?controller=product&action=show&id=' . $product_id);
        }
    }

    public function update() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $id = $_POST['id'];
        $quantity = (int)$_POST['quantity'];

        try {
            $this->cartModel->updateCart($id, $quantity);
            $_SESSION['success'] = "Cập nhật giỏ hàng thành công!";
            header('Location: ?controller=cart&action=index');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?controller=cart&action=index');
        }
    }

    public function delete() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $id = $_GET['id'];
        $this->cartModel->deleteFromCart($id);
        $_SESSION['success'] = "Xóa sản phẩm khỏi giỏ hàng thành công!";
        header('Location: ?controller=cart&action=index');
    }
}