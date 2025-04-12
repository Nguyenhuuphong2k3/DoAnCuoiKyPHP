<?php
require_once '../app/config/database.php';

class ProductModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll($page, $perPage) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LIMIT :offset, :perPage";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotal() {
        $query = "SELECT COUNT(*) FROM products";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getByCategory($category_id, $page, $perPage) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :category_id LIMIT :offset, :perPage";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalByCategory($category_id) {
        $query = "SELECT COUNT(*) FROM products WHERE category_id = :category_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create($name, $category_id, $description, $price, $discount, $stock, $image) {
        $query = "INSERT INTO products (name, category_id, description, price, discount, stock, image) VALUES (:name, :category_id, :description, :price, :discount, :stock, :image)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':image', $image);
        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $category_id, $description, $price, $discount, $stock, $image) {
        $query = "UPDATE products SET name = :name, category_id = :category_id, description = :description, price = :price, discount = :discount, stock = :stock, image = :image WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':image', $image);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function search($keyword) {
        $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE :keyword";
        $stmt = $this->db->prepare($query);
        $keyword = "%$keyword%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}