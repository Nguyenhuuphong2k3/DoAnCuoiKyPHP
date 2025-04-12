<?php
require_once '../app/config/database.php';

class OrderModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function createOrder($user_id, $total) {
        $query = "INSERT INTO orders (user_id, total) VALUES (:user_id, :total)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total', $total);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function createOrderDetail($order_id, $product_id, $quantity, $price) {
        $query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        return $stmt->execute();
    }

    public function getOrdersByUser($user_id) {
        $query = "SELECT o.*, od.product_id, od.quantity, od.price, p.name, p.image 
                  FROM orders o 
                  JOIN order_details od ON o.id = od.order_id 
                  JOIN products p ON od.product_id = p.id 
                  WHERE o.user_id = :user_id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}