<?php
require_once '../app/models/ProductModel.php';
require_once '../app/models/CategoryModel.php';
require_once '../app/models/OrderModel.php';
require_once '../app/models/AddressModel.php';
require_once '../app/models/CartModel.php';

class ApiController {
    private $productModel;
    private $categoryModel;
    private $orderModel;
    private $addressModel;
    private $cartModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->orderModel = new OrderModel();
        $this->addressModel = new AddressModel();
        $this->cartModel = new CartModel();
    }

    // Lấy danh sách sản phẩm
    public function getProducts() {
        header('Content-Type: application/json');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $products = $this->productModel->getAll($page, $limit);
        echo json_encode($products);
    }

    // Lấy thông tin chi tiết sản phẩm
    public function getProduct() {
        header('Content-Type: application/json');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product ID']);
            return;
        }
        $product = $this->productModel->getById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }
        echo json_encode($product);
    }

    // Lấy danh sách danh mục
    public function getCategories() {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getAll();
        echo json_encode($categories);
    }

    // Lấy danh sách đơn hàng của người dùng
    public function getOrders() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $user_id = $_SESSION['user']['id'];
        $orders = $this->orderModel->getOrdersByUser($user_id);
        echo json_encode($orders);
    }

    // Lấy danh sách địa chỉ của người dùng
    public function getAddresses() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $user_id = $_SESSION['user']['id'];
        $addresses = $this->addressModel->getAddressesByUser($user_id);
        echo json_encode($addresses);
    }

    // Thêm địa chỉ giao hàng
    public function addAddress() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user_id = $_SESSION['user']['id'];
        $data = json_decode(file_get_contents('php://input'), true);

        $address = isset($data['address']) ? trim($data['address']) : '';
        $is_default = isset($data['is_default']) ? (int)$data['is_default'] : 0;

        if (empty($address)) {
            http_response_code(400);
            echo json_encode(['error' => 'Address is required']);
            return;
        }

        if ($this->addressModel->checkDuplicateAddress($user_id, $address)) {
            http_response_code(400);
            echo json_encode(['error' => 'Address already exists']);
            return;
        }

        $result = $this->addressModel->createAddress($user_id, $address, $is_default);
        if ($result) {
            echo json_encode(['success' => 'Address added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add address']);
        }
    }

    // Cập nhật địa chỉ giao hàng
    public function updateAddress() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user_id = $_SESSION['user']['id'];
        $address_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$address_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing address ID']);
            return;
        }

        $address = $this->addressModel->getAddressByIdAndUser($address_id, $user_id);
        if (!$address) {
            http_response_code(404);
            echo json_encode(['error' => 'Address not found or does not belong to you']);
            return;
        }

        $new_address = isset($data['address']) ? trim($data['address']) : '';
        $is_default = isset($data['is_default']) ? (int)$data['is_default'] : 0;

        if (empty($new_address)) {
            http_response_code(400);
            echo json_encode(['error' => 'Address is required']);
            return;
        }

        if ($address['address'] !== $new_address && $this->addressModel->checkDuplicateAddress($user_id, $new_address)) {
            http_response_code(400);
            echo json_encode(['error' => 'Address already exists']);
            return;
        }

        $result = $this->addressModel->updateAddress($address_id, $user_id, $new_address, $is_default);
        if ($result) {
            echo json_encode(['success' => 'Address updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update address']);
        }
    }

    // Xóa địa chỉ giao hàng
    public function deleteAddress() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user_id = $_SESSION['user']['id'];
        $address_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$address_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing address ID']);
            return;
        }

        $address = $this->addressModel->getAddressByIdAndUser($address_id, $user_id);
        if (!$address) {
            http_response_code(404);
            echo json_encode(['error' => 'Address not found or does not belong to you']);
            return;
        }

        $result = $this->addressModel->deleteAddress($user_id, $address_id);
        if ($result) {
            echo json_encode(['success' => 'Address deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete address']);
        }
    }

    // Lấy giỏ hàng
    public function getCart() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user_id = $_SESSION['user']['id'];
        $cartItems = $this->cartModel->getCart($user_id);
        echo json_encode($cartItems);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user_id = $_SESSION['user']['id'];
        $data = json_decode(file_get_contents('php://input'), true);

        $product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

        if (!$product_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product ID']);
            return;
        }

        if ($quantity <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid quantity']);
            return;
        }

        try {
            $result = $this->cartModel->addToCart($user_id, $product_id, $quantity);
            if ($result) {
                echo json_encode(['success' => 'Product added to cart successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add product to cart']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Thanh toán (tạo đơn hàng)
    public function checkout() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user_id = $_SESSION['user']['id'];
        $data = json_decode(file_get_contents('php://input'), true);

        $address_id = isset($data['address_id']) ? (int)$data['address_id'] : null;
        $payment_method = isset($data['payment_method']) ? $data['payment_method'] : 'cod';

        if (!$address_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing address ID']);
            return;
        }

        $cartItems = $this->cartModel->getCart($user_id);
        if (empty($cartItems)) {
            http_response_code(400);
            echo json_encode(['error' => 'Cart is empty']);
            return;
        }

        $address = $this->addressModel->getAddressByIdAndUser($address_id, $user_id);
        if (!$address) {
            http_response_code(400);
            echo json_encode(['error' => 'Address not found or does not belong to you']);
            return;
        }

        try {
            // Kiểm tra tồn kho
            foreach ($cartItems as $item) {
                $product = $this->productModel->getById($item['product_id']);
                if (!$product) {
                    throw new Exception("Product not found: " . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'));
                }
                if ($product['stock'] < $item['quantity']) {
                    throw new Exception("Product out of stock: " . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . " (remaining: " . $product['stock'] . ")");
                }
            }

            // Tính tổng tiền
            $total = 0;
            foreach ($cartItems as $item) {
                $price = isset($item['price']) && is_numeric($item['price']) ? (float)$item['price'] : 0;
                $quantity = isset($item['quantity']) && is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
                $discount = isset($item['discount']) && is_numeric($item['discount']) ? (float)$item['discount'] : 0;
                $itemTotal = $price * $quantity * (1 - $discount / 100);
                if ($itemTotal < 0) {
                    throw new Exception("Invalid total for product: " . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'));
                }
                $total += $itemTotal;
            }

            if ($total <= 0) {
                throw new Exception("Invalid order total");
            }

            if ($payment_method === 'momo') {
                // Tạm thời không xử lý thanh toán MoMo qua API
                http_response_code(400);
                echo json_encode(['error' => 'MoMo payment not supported via API yet']);
                return;
            }

            // Tạo đơn hàng
            $order_id = $this->orderModel->createOrder($user_id, $total, $address_id);

            if (!$order_id) {
                throw new Exception("Failed to create order");
            }

            // Tạo chi tiết đơn hàng
            foreach ($cartItems as $item) {
                $createDetailSuccess = $this->orderModel->createOrderDetail($order_id, $item['product_id'], $item['quantity'], $item['price']);
                if (!$createDetailSuccess) {
                    throw new Exception("Failed to create order detail for product ID: " . $item['product_id']);
                }
                $updateStockSuccess = $this->productModel->updateStock($item['product_id'], $item['quantity']);
                if (!$updateStockSuccess) {
                    throw new Exception("Failed to update stock for product ID: " . $item['product_id']);
                }
            }

            // Xóa giỏ hàng
            $clearCartSuccess = $this->cartModel->clearCart($user_id);
            if (!$clearCartSuccess) {
                throw new Exception("Failed to clear cart");
            }

            echo json_encode(['success' => 'Order created successfully', 'order_id' => $order_id]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}