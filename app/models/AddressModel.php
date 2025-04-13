<?php
require_once __DIR__ . '/../config/database.php';

class AddressModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        if ($this->conn === null) {
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }

    public function getAddressesByUser($user_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Kiểm tra địa chỉ trùng
    public function checkDuplicateAddress($user_id, $address) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM addresses WHERE user_id = ? AND address = ?");
            $stmt->execute([$user_id, $address]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            echo "Error in checkDuplicateAddress: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function resetDefaultAddress($user_id) {
        try {
            $stmt = $this->conn->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            echo "Error in resetDefaultAddress: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function createAddress($user_id, $address, $is_default = false) {
        try {
            if ($is_default) {
                $this->resetDefaultAddress($user_id);
            }

            $stmt = $this->conn->prepare("INSERT INTO addresses (user_id, address, is_default) VALUES (?, ?, ?)");
            return $stmt->execute([$user_id, $address, $is_default]);
        } catch (PDOException $e) {
            echo "Error in createAddress: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function getAddressByIdAndUser($address_id, $user_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$address_id, $user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error in getAddressByIdAndUser: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function updateAddress($address_id, $user_id, $address, $is_default) {
        try {
            if ($is_default) {
                $this->resetDefaultAddress($user_id);
            }

            $stmt = $this->conn->prepare("UPDATE addresses SET address = ?, is_default = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([$address, $is_default, $address_id, $user_id]);
        } catch (PDOException $e) {
            echo "Error in updateAddress: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function deleteAddress($user_id, $address_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
            return $stmt->execute([$address_id, $user_id]);
        } catch (PDOException $e) {
            echo "Error in deleteAddress: " . $e->getMessage() . "<br>";
            return false;
        }
    }
}