<?php
require_once '../app/config/database.php';

class ReviewModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        if (!$this->db) {
            throw new Exception("Không thể kết nối đến database.");
        }
    }

    public function create($user_id, $product_id, $rating, $comment) {
        try {
            $query = "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (:user_id, :product_id, :rating, :comment)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi trong create review: " . $e->getMessage());
            return false;
        }
    }

    public function getByProduct($product_id) {
        try {
            $query = "SELECT r.*, u.username 
                      FROM reviews r 
                      JOIN users u ON r.user_id = u.id 
                      WHERE r.product_id = :product_id 
                      ORDER BY r.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi trong getByProduct: " . $e->getMessage());
            return [];
        }
    }
}