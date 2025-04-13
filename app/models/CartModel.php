<?php
require_once '../app/config/database.php';
require_once '../app/models/ProductModel.php';

class CartModel {
    private $db;
    private $productModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel();
    }

    public function getCart($user_id) {
        $query = "SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.discount, p.image, p.stock 
                  FROM carts c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart($user_id, $product_id, $quantity) {
        // Kiểm tra số lượng tồn kho
        $product = $this->productModel->getById($product_id);
        $currentStock = $product['stock'];

        // Lấy số lượng hiện tại trong giỏ hàng (nếu có)
        $query = "SELECT quantity FROM carts WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentCartQuantity = $cartItem ? $cartItem['quantity'] : 0;

        // Tổng số lượng (hiện tại trong giỏ + số lượng mới)
        $totalQuantity = $currentCartQuantity + $quantity;

        if ($totalQuantity > $currentStock) {
            throw new Exception("Số lượng tồn kho không đủ! Hiện tại chỉ còn: $currentStock");
        }

        // Thêm hoặc cập nhật giỏ hàng
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
        // Lấy thông tin giỏ hàng để biết product_id
        $query = "SELECT product_id, quantity FROM carts WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        $product_id = $cartItem['product_id'];

        // Kiểm tra số lượng tồn kho
        $product = $this->productModel->getById($product_id);
        $currentStock = $product['stock'];

        if ($quantity > $currentStock) {
            throw new Exception("Số lượng tồn kho không đủ! Hiện tại chỉ còn: $currentStock");
        }

        // Cập nhật số lượng trong giỏ hàng
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