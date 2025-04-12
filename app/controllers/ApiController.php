<?php
require_once '../app/models/ProductModel.php';
require_once '../app/models/CategoryModel.php';
require_once '../app/models/OrderModel.php';

class ApiController {
    private $productModel;
    private $categoryModel;
    private $orderModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->orderModel = new OrderModel();
    }

    public function getProducts() {
        header('Content-Type: application/json');
        $products = $this->productModel->getAll(1, 10);
        echo json_encode($products);
    }

    public function getProduct() {
        header('Content-Type: application/json');
        $id = $_GET['id'];
        $product = $this->productModel->getById($id);
        echo json_encode($product);
    }

    public function getCategories() {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getAll();
        echo json_encode($categories);
    }

    public function getOrders() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $user_id = $_SESSION['user']['id'];
        $orders = $this->orderModel->getOrdersByUser($user_id);
        echo json_encode($orders);
    }
}