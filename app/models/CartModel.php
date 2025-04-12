<?php
require_once '../app/config/database.php';

class CartModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getCart($user_id) {
        $query = "SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.discount, p.image 
                  FROM carts c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart($user_id, $product_id, $quantity) {
        $query = "SELECT * FROM carts WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $query = "UPDATE carts SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id";
        } else {
            $query = "INSERT INTO carts (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }

    public function updateCart($id, $quantity) {
        $query = "UPDATE carts SET quantity = :quantity WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }

    public function deleteFromCart($id) {
        $query = "DELETE FROM carts WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function clearCart($user_id) {
        $query = "DELETE FROM carts WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}