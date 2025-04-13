<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/AddressModel.php';

class OrderController {
    private $orderModel;
    private $cartModel;
    private $productModel;
    private $addressModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->addressModel = new AddressModel();
    }

    public function checkout() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $cartItems = $this->cartModel->getCart($user_id);

        if (empty($cartItems)) {
            $_SESSION['error'] = "Giỏ hàng trống!";
            header('Location: ?controller=cart&action=index');
            exit;
        }

        $address_id = $_POST['address_id'] ?? null;
        $payment_method = $_POST['payment_method'] ?? 'cod';

        // Kiểm tra address_id hợp lệ
        if (!$address_id) {
            $_SESSION['error'] = "Bạn phải chọn địa chỉ giao hàng!";
            header('Location: ?controller=cart&action=index');
            exit;
        }

        // Kiểm tra address_id có tồn tại trong bảng addresses
        $address = $this->addressModel->getAddressByIdAndUser($address_id, $user_id);
        if (!$address) {
            $_SESSION['error'] = "Địa chỉ giao hàng không tồn tại hoặc không thuộc về bạn!";
            header('Location: ?controller=cart&action=index');
            exit;
        }

        // Chỉ xử lý cho COD
        if ($payment_method !== 'cod') {
            $_SESSION['error'] = "Phương thức thanh toán không hợp lệ!";
            header('Location: ?controller=cart&action=index');
            exit;
        }

        try {
            // Kiểm tra tồn kho
            foreach ($cartItems as $item) {
                $product = $this->productModel->getById($item['product_id']);
                if (!$product) {
                    throw new Exception("Sản phẩm không tồn tại: " . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'));
                }
                if ($product['stock'] < $item['quantity']) {
                    throw new Exception("Sản phẩm: " . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . " không đủ hàng (còn lại: " . $product['stock'] . ")");
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
                    throw new Exception("Tổng tiền của sản phẩm " . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . " không hợp lệ!");
                }
                $total += $itemTotal;
            }

            if ($total <= 0) {
                throw new Exception("Tổng tiền đơn hàng không hợp lệ!");
            }

            // Tạo đơn hàng
            $order_id = $this->orderModel->createOrder($user_id, $total, $address_id);

            if (!$order_id) {
                error_log("Failed to create order for user_id: $user_id, total: $total, address_id: $address_id");
                throw new Exception("Không thể tạo đơn hàng! Vui lòng kiểm tra dữ liệu.");
            }

            // Tạo chi tiết đơn hàng
            foreach ($cartItems as $item) {
                $createDetailSuccess = $this->orderModel->createOrderDetail($order_id, $item['product_id'], $item['quantity'], $item['price']);
                if (!$createDetailSuccess) {
                    error_log("Failed to create order detail for order_id: $order_id, product_id: " . $item['product_id']);
                    throw new Exception("Không thể tạo chi tiết đơn hàng!");
                }
                $updateStockSuccess = $this->productModel->updateStock($item['product_id'], $item['quantity']);
                if (!$updateStockSuccess) {
                    error_log("Failed to update stock for product_id: " . $item['product_id']);
                    throw new Exception("Không thể cập nhật tồn kho!");
                }
            }

            // Xóa giỏ hàng
            $clearCartSuccess = $this->cartModel->clearCart($user_id);
            if (!$clearCartSuccess) {
                error_log("Failed to clear cart for user_id: $user_id");
                throw new Exception("Không thể xóa giỏ hàng!");
            }

            $_SESSION['success'] = "Thanh toán thành công!";
            header('Location: ?controller=order&action=history');
            exit;
        } catch (Exception $e) {
            error_log("Checkout error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?controller=cart&action=index');
            exit;
        }
    }

    public function history() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $orders = $this->orderModel->getOrdersByUser($user_id);
        require_once __DIR__ . '/../views/order/history.php';
    }

    public function momoPayment() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $address_id = $_POST['address_id'] ?? null;
        $cartItems = $this->cartModel->getCart($user_id);

        if (!$address_id) {
            $_SESSION['error'] = "Bạn phải chọn địa chỉ giao hàng!";
            header('Location: ?controller=cart&action=index');
            exit;
        }

        if (empty($cartItems)) {
            $_SESSION['error'] = "Giỏ hàng trống!";
            header('Location: ?controller=cart&action=index');
            exit;
        }

        // Tính tổng tiền
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'] * (1 - $item['discount'] / 100);
        }

        $_SESSION['momo_address_id'] = $address_id;

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = "MOMO";
        $accessKey = "F8BBA842ECF85";
        $secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";

        $orderInfo = "Thanh toán đơn hàng phụ kiện";
        $amount = (int)$total; // Chuyển tổng tiền thành số nguyên
        $orderId = time() . "";
        $requestId = time() . "";
        $redirectUrl = "http://doancuoikyphp-main.test/?controller=order&action=momoResult";
        $ipnUrl = "http://doancuoikyphp-main.test/?controller=order&action=momoResult";

        $extraData = "";

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData"
            . "&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo"
            . "&partnerCode=$partnerCode&redirectUrl=$redirectUrl"
            . "&requestId=$requestId&requestType=captureWallet";

        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature,
            'lang' => 'vi'
        ];

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['payUrl'])) {
            header('Location: ' . $jsonResult['payUrl']);
            exit;
        } else {
            $_SESSION['error'] = "Không thể tạo yêu cầu thanh toán MoMo!";
            header('Location: ?controller=cart&action=index');
            exit;
        }
    }

    public function momoResult() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        if (isset($_GET['resultCode']) && $_GET['resultCode'] == 0) {
            $user_id = $_SESSION['user']['id'];
            $cartItems = $this->cartModel->getCart($user_id);

            if (empty($cartItems)) {
                $_SESSION['error'] = "Giỏ hàng trống!";
                header('Location: ?controller=cart&action=index');
                exit;
            }

            $address_id = $_SESSION['momo_address_id'] ?? null;

            if (!$address_id) {
                $_SESSION['error'] = "Không tìm thấy địa chỉ giao hàng!";
                header('Location: ?controller=cart&action=index');
                exit;
            }

            try {
                foreach ($cartItems as $item) {
                    $product = $this->productModel->getById($item['product_id']);
                    if ($product['stock'] < $item['quantity']) {
                        throw new Exception("Sản phẩm " . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . " không đủ hàng!");
                    }
                }

                $total = 0;
                foreach ($cartItems as $item) {
                    $total += $item['price'] * $item['quantity'] * (1 - $item['discount'] / 100);
                }

                $order_id = $this->orderModel->createOrder($user_id, $total, $address_id);

                if (!$order_id) {
                    throw new Exception("Không thể tạo đơn hàng MoMo!");
                }

                foreach ($cartItems as $item) {
                    $this->orderModel->createOrderDetail($order_id, $item['product_id'], $item['quantity'], $item['price']);
                    $this->productModel->updateStock($item['product_id'], $item['quantity']);
                }

                $this->cartModel->clearCart($user_id);
                unset($_SESSION['momo_address_id']);
                $_SESSION['success'] = "Thanh toán MoMo thành công!";
                header('Location: ?controller=order&action=history');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi: " . $e->getMessage();
                header('Location: ?controller=cart&action=index');
                exit;
            }
        } else {
            $_SESSION['error'] = "Thanh toán thất bại hoặc bị hủy!";
            header('Location: ?controller=cart&action=index');
            exit;
        }
    }

    public function confirmAddress() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $addresses = $this->addressModel->getAddressesByUser($user_id);
        require_once __DIR__ . '/../views/order/confirm_address.php';
    }

    public function saveAddress() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $address = trim($_POST['address'] ?? '');
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            $user_id = $_SESSION['user']['id'];

            if (empty($address)) {
                $_SESSION['error'] = "Địa chỉ không được để trống!";
                header("Location: ?controller=order&action=confirmAddress");
                exit;
            }

            if ($this->addressModel->checkDuplicateAddress($user_id, $address)) {
                $_SESSION['error'] = "Địa chỉ này đã tồn tại!";
                header("Location: ?controller=order&action=confirmAddress");
                exit;
            }

            $result = $this->addressModel->createAddress($user_id, $address, $is_default);

            if ($result) {
                $_SESSION['success'] = "Thêm địa chỉ thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm địa chỉ.";
            }
            header("Location: ?controller=order&action=confirmAddress");
            exit;
        }
    }

    public function editAddress() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $address_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user']['id'];

        if (!$address_id) {
            $_SESSION['error'] = "Không tìm thấy địa chỉ để sửa.";
            header("Location: ?controller=order&action=confirmAddress");
            exit;
        }

        $address = $this->addressModel->getAddressByIdAndUser($address_id, $user_id);

        if (!$address) {
            $_SESSION['error'] = "Địa chỉ không tồn tại hoặc không thuộc về bạn.";
            header("Location: ?controller=order&action=confirmAddress");
            exit;
        }

        require_once __DIR__ . '/../views/order/edit_address.php';
    }

    public function updateAddress() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $address_id = $_POST['address_id'] ?? null;
            $address = trim($_POST['address'] ?? '');
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            $user_id = $_SESSION['user']['id'];

            if (!$address_id || empty($address)) {
                $_SESSION['error'] = "Dữ liệu không hợp lệ.";
                header("Location: ?controller=order&action=editAddress&id=" . urlencode($address_id));
                exit;
            }

            // Kiểm tra địa chỉ tồn tại và thuộc về user
            $current_address = $this->addressModel->getAddressByIdAndUser($address_id, $user_id);
            if (!$current_address) {
                $_SESSION['error'] = "Địa chỉ không tồn tại hoặc không thuộc về bạn.";
                header("Location: ?controller=order&action=confirmAddress");
                exit;
            }

            // Kiểm tra trùng lặp (chỉ nếu địa chỉ thay đổi)
            if ($current_address['address'] !== $address && $this->addressModel->checkDuplicateAddress($user_id, $address)) {
                $_SESSION['error'] = "Địa chỉ này đã tồn tại!";
                header("Location: ?controller=order&action=editAddress&id=" . urlencode($address_id));
                exit;
            }

            $result = $this->addressModel->updateAddress($address_id, $user_id, $address, $is_default);

            if ($result) {
                $_SESSION['success'] = "Cập nhật địa chỉ thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi cập nhật địa chỉ.";
            }
            header("Location: ?controller=order&action=confirmAddress");
            exit;
        }
    }

    public function deleteAddress() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $address_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user']['id'];

        if (!$address_id) {
            $_SESSION['error'] = "Không tìm thấy địa chỉ để xóa.";
            header("Location: ?controller=order&action=confirmAddress");
            exit;
        }

        // Kiểm tra địa chỉ có tồn tại và thuộc về user
        $address = $this->addressModel->getAddressByIdAndUser($address_id, $user_id);
        if (!$address) {
            $_SESSION['error'] = "Địa chỉ không tồn tại hoặc không thuộc về bạn.";
            header("Location: ?controller=order&action=confirmAddress");
            exit;
        }

        $result = $this->addressModel->deleteAddress($user_id, $address_id);

        if ($result) {
            $_SESSION['success'] = "Xóa địa chỉ thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa địa chỉ. Vui lòng thử lại.";
        }
        header("Location: ?controller=order&action=confirmAddress");
        exit;
    }

    private function execPostRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}