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
        $quantity = $_POST['quantity'];
        $this->cartModel->addToCart($user_id, $product_id, $quantity);
        header('Location: ?controller=cart&action=index');
    }

    public function update() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $id = $_POST['id'];
        $quantity = $_POST['quantity'];
        $this->cartModel->updateCart($id, $quantity);
        header('Location: ?controller=cart&action=index');
    }

    public function delete() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $id = $_GET['id'];
        $this->cartModel->deleteFromCart($id);
        header('Location: ?controller=cart&action=index');
    }
}