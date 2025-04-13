<?php
require_once '../app/models/ProductModel.php';
require_once '../app/models/CategoryModel.php';

class ProductController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function list() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 8; // Sửa thành 8 sản phẩm mỗi trang
        $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

        if ($page < 1) {
            $page = 1; // Đảm bảo số trang không nhỏ hơn 1
        }

        if ($category_id) {
            $products = $this->productModel->getByCategory($category_id, $page, $perPage);
            $total = $this->productModel->getTotalByCategory($category_id);
        } else {
            $products = $this->productModel->getAll($page, $perPage);
            $total = $this->productModel->getTotal();
        }

        $totalPages = ceil($total / $perPage);

        // Đảm bảo truyền đầy đủ biến sang view
        require_once '../app/views/product/list.php';
    }

    public function add() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $success = false;
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $discount = $_POST['discount'] ?? 0;
            $stock = $_POST['stock'] ?? '';
            $image = $_FILES['image']['name'] ?? '';

            if (empty($name) || empty($category_id) || empty($description) || empty($price) || empty($stock) || empty($image)) {
                $error = "Vui lòng điền đầy đủ thông tin.";
            } else {
                $uploadDir = '../public/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $uploadFile = $uploadDir . basename($image);
                if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $result = $this->productModel->create($name, $category_id, $description, $price, $discount, $stock, $image);
                        if ($result) {
                            $success = true;
                            header('Location: ?controller=product&action=list');
                            exit;
                        } else {
                            $error = "Có lỗi xảy ra khi thêm sản phẩm.";
                            unlink($uploadFile);
                        }
                    } else {
                        $error = "Không thể upload hình ảnh. Vui lòng kiểm tra quyền thư mục.";
                    }
                } else {
                    $error = "Vui lòng chọn một hình ảnh.";
                }
            }
        }

        require_once '../app/views/product/add.php';
    }

    public function edit() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $id = $_GET['id'];
        $product = $this->productModel->getById($id);
        $categories = $this->categoryModel->getAll();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $discount = $_POST['discount'];
            $stock = $_POST['stock'];
            $image = $product['image'];
            if ($_FILES['image']['name']) {
                $image = $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], '../public/images/' . $image);
            }
            $this->productModel->update($id, $name, $category_id, $description, $price, $discount, $stock, $image);
            header('Location: ?controller=product&action=list');
        }
        require_once '../app/views/product/edit.php';
    }

    public function delete() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $id = $_GET['id'];
        $this->productModel->delete($id);
        header('Location: ?controller=product&action=list');
    }

    public function show() {
        $id = $_GET['id'];
        $product = $this->productModel->getById($id);
        require_once '../app/views/product/show.php';
    }

    public function search() {
        $keyword = $_GET['keyword'];
        $products = $this->productModel->search($keyword);
        require_once '../app/views/product/list.php';
    }
}