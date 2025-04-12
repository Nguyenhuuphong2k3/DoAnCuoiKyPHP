<?php
require_once '../app/models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->userModel->login($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                header('Location: ?controller=default&action=index');
            } else {
                $error = "Invalid credentials!";
            }
        }
        require_once '../app/views/user/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = 'user';
            if ($this->userModel->register($username, $email, $password, $role)) {
                header('Location: ?controller=user&action=login');
            } else {
                $error = "Registration failed!";
            }
        }
        require_once '../app/views/user/register.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ?controller=user&action=login');
    }
}