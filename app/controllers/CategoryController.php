<?php
require_once '../app/models/CategoryModel.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }

    public function list() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $categories = $this->categoryModel->getAll();
        require_once '../app/views/category/list.php';
    }

    public function add() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ?controller=user&action=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $this->categoryModel->create($name);
            header('Location: ?controller=category&action=list');
        }
        require_once '../app/views/category/add.php';
    }

    public function edit() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $id = $_GET['id'];
        $category = $this->categoryModel->getById($id);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $this->categoryModel->update($id, $name);
            header('Location: ?controller=category&action=list');
        }
        require_once '../app/views/category/edit.php';
    }

    public function delete() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $id = $_GET['id'];
        $this->categoryModel->delete($id);
        header('Location: ?controller=category&action=list');
    }
}