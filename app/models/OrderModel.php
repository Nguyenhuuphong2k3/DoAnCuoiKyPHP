<?php
require_once __DIR__ . '/../config/database.php';

class OrderModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
        if ($this->conn === null) {
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }

    // Hàm tạo đơn hàng chung cho cả đơn thường và MoMo
    public function createOrderGeneral($data)
    {
        try {
            // Đảm bảo các giá trị hợp lệ
            $data['user_id'] = (int)($data['user_id'] ?? 0);
            $data['order_id'] = $data['order_id'] ?? null;
            $data['amount'] = isset($data['amount']) ? (float)$data['amount'] : (float)($data['total'] ?? 0);
            $data['total'] = (float)($data['total'] ?? 0);
            $data['address_id'] = (int)($data['address_id'] ?? 0);
            $data['status'] = $data['status'] ?? 'pending';

            // Kiểm tra giá trị trước khi thực hiện truy vấn
            if ($data['user_id'] <= 0) {
                throw new Exception("Invalid user_id: " . $data['user_id']);
            }
            if ($data['address_id'] <= 0) {
                throw new Exception("Invalid address_id: " . $data['address_id']);
            }
            if ($data['total'] <= 0) {
                throw new Exception("Invalid total: " . $data['total']);
            }

            $query = "
                INSERT INTO orders (user_id, order_id, amount, status, address_id, total, created_at)
                VALUES (:user_id, :order_id, :amount, :status, :address_id, :total, NOW())
            ";

            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':order_id', $data['order_id'], PDO::PARAM_STR);
            $stmt->bindValue(':amount', $data['amount'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindValue(':address_id', $data['address_id'], PDO::PARAM_INT);
            $stmt->bindValue(':total', $data['total'], PDO::PARAM_STR);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in createOrderGeneral: " . $e->getMessage());
            error_log("Data: " . json_encode($data));
            return false;
        } catch (Exception $e) {
            error_log("Validation error in createOrderGeneral: " . $e->getMessage());
            return false;
        }
    }

    // Tạo chi tiết đơn hàng
    public function createOrderDetail($order_id, $product_id, $quantity, $price)
    {
        try {
            $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                      VALUES (:order_id, :product_id, :quantity, :price)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in createOrderDetail: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách đơn hàng theo user, bao gồm địa chỉ giao hàng
    public function getOrdersByUser($user_id)
    {
        try {
            $query = "SELECT o.*, od.product_id, od.quantity, od.price, p.name, p.image, p.discount, a.address 
                      FROM orders o 
                      JOIN order_details od ON o.id = od.order_id 
                      JOIN products p ON od.product_id = p.id 
                      LEFT JOIN addresses a ON o.address_id = a.id 
                      WHERE o.user_id = :user_id 
                      ORDER BY o.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getOrdersByUser: " . $e->getMessage());
            return [];
        }
    }

    // Hàm createOrder đơn giản gọi createOrderGeneral cho tương thích controller cũ
    public function createOrder($user_id, $total, $address_id)
    {
        $data = [
            'user_id' => $user_id,
            'total' => $total,
            'address_id' => $address_id,
            'status' => 'pending'
        ];
        return $this->createOrderGeneral($data);
    }
}