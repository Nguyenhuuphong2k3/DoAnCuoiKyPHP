<?php
session_start();
require_once '../app/config/database.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'default';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

$controller = ucfirst($controller) . 'Controller';
$controllerFile = "../app/controllers/$controller.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controller();
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        die("Action not found!");
    }
} else {
    die("Controller not found!");
}