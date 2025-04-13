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
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính giá sau khuyến mãi cho từng sản phẩm
        foreach ($products as &$product) {
            $price = $product['price'];
            $discount = $product['discount'];
            $discountAmount = ($price * $discount) / 100;
            $finalPrice = $price - $discountAmount;
            $product['final_price'] = $finalPrice; // Lưu giá đã tính khuyến mãi
        }

        return $products;
    }

    public function getTotal() {
        $query = "SELECT COUNT(*) FROM products";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getByCategory($categoryId, $limit = 4) {
        try {
            $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :category_id ORDER BY p.id DESC LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tính giá sau khuyến mãi cho từng sản phẩm
            foreach ($products as &$product) {
                $price = $product['price'];
                $discount = $product['discount'];
                $discountAmount = ($price * $discount) / 100;
                $finalPrice = $price - $discountAmount;
                $product['final_price'] = $finalPrice; // Lưu giá đã tính khuyến mãi
            }

            return $products;
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy sản phẩm theo thể loại: " . $e->getMessage());
            return false;
        }
    }

    public function getByCategoryWithPagination($category_id, $page, $perPage) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :category_id LIMIT :offset, :perPage";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính giá sau khuyến mãi cho từng sản phẩm
        foreach ($products as &$product) {
            $price = $product['price'];
            $discount = $product['discount'];
            $discountAmount = ($price * $discount) / 100;
            $finalPrice = $price - $discountAmount;
            $product['final_price'] = $finalPrice; // Lưu giá đã tính khuyến mãi
        }

        return $products;
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
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính giá sau khuyến mãi cho từng sản phẩm
        foreach ($products as &$product) {
            $price = $product['price'];
            $discount = $product['discount'];
            $discountAmount = ($price * $discount) / 100;
            $finalPrice = $price - $discountAmount;
            $product['final_price'] = $finalPrice; // Lưu giá đã tính khuyến mãi
        }

        return $products;
    }

    public function updateStock($product_id, $quantity) {
        $product = $this->getById($product_id);
        $currentStock = $product['stock'];
        $newStock = $currentStock - $quantity;

        if ($newStock < 0) {
            throw new Exception("Số lượng tồn kho không đủ cho sản phẩm ID: $product_id. Hiện tại chỉ còn: $currentStock");
        }

        $query = "UPDATE products SET stock = :stock WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':stock', $newStock);
        $stmt->bindParam(':id', $product_id);
        return $stmt->execute();
    }
}
