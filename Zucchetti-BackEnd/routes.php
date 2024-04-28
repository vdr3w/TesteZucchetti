<?php
// routes.php
require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

use MyProject\Controller\ProductController;
use MyProject\Controller\CustomerController;
use MyProject\Controller\PaymentMethodController;
use MyProject\Controller\SaleController;


$entityManager = GetEntityManager();
$productController = new ProductController($entityManager);
$customerController = new CustomerController($entityManager);
$paymentMethodController = new PaymentMethodController($entityManager);
$saleController = new SaleController($entityManager);

// Capturando o corpo da requisição
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Capturando o método HTTP e o URI da requisição
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestURI = $_SERVER['REQUEST_URI'];

// Parse do URI para roteamento
$parsedUrl = parse_url($requestURI);
$path = $parsedUrl['path'];

// Roteamento
switch ($path) {
        // ROTAS PRODUTOS
    case '/product/create':
        if ($requestMethod == 'POST') {
            $response = $productController->createProduct($data['name'], $data['price'], $data['quantity']);
            echo $response;
        }
        break;
    case '/product/list':
        if ($requestMethod == 'GET') {
            $response = $productController->listProducts();
            echo $response;
        }
        break;
    case '/product/show':
        if ($requestMethod == 'GET' && isset($_GET['id'])) {
            $response = $productController->showProduct($_GET['id']);
            echo $response;
        }
        break;
    case '/product/update':
        if ($requestMethod == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['id'], $data['name'], $data['price'], $data['quantity'])) {
                $response = $productController->updateProduct($data['id'], $data['name'], $data['price'], $data['quantity']);
                echo $response;
            } else {
                http_response_code(400);
                echo "Missing data for updating product.";
            }
        }
        break;
    case '/product/delete':
        if ($requestMethod == 'POST' && isset($_GET['id'])) {
            $response = $productController->deleteProduct($_GET['id']);
            echo $response;
        }
        break;

        // ROTAS CLIENTES
    case '/customer/create':
        if ($requestMethod == 'POST') {
            $response = $customerController->createCustomer();
            echo $response;
        }
        break;
    case '/customer/list':
        if ($requestMethod == 'GET') {
            $response = $customerController->listCustomers();
            echo $response;
        }
        break;
    case '/customer/show':
        if ($requestMethod == 'GET' && isset($_GET['id'])) {
            $response = $customerController->showCustomer($_GET['id']);
            echo $response;
        }
        break;
    case '/customer/update':
        if ($requestMethod == 'POST' && isset($_GET['id'])) {
            $response = $customerController->updateCustomer($_GET['id']);
            echo $response;
        }
        break;
    case '/customer/delete':
        if ($requestMethod == 'POST' && isset($_GET['id'])) {
            $response = $customerController->deleteCustomer($_GET['id']);
            echo $response;
        }
        break;

        // ROTAS PARA PAYMENT METHODS
    case '/payment-method/create':
        if ($requestMethod == 'POST') {
            $response = $paymentMethodController->createPaymentMethod();
            echo $response;
        }
        break;
    case '/payment-method/list':
        if ($requestMethod == 'GET') {
            $response = $paymentMethodController->listPaymentMethods();
            echo $response;
        }
        break;
    case '/payment-method/show':
        if ($requestMethod == 'GET' && isset($_GET['id'])) {
            $response = $paymentMethodController->showPaymentMethod($_GET['id']);
            echo $response;
        }
        break;
    case '/payment-method/update':
        if ($requestMethod == 'POST' && isset($_GET['id'])) {
            $response = $paymentMethodController->updatePaymentMethod($_GET['id']);
            echo $response;
        }
        break;
    case '/payment-method/delete':
        if ($requestMethod == 'POST' && isset($_GET['id'])) {
            $response = $paymentMethodController->deletePaymentMethod($_GET['id']);
            echo $response;
        }
        break;

        // Rotas para Sale
    case '/sale/create':
        if ($requestMethod == 'POST') {
            $response = $saleController->createSale();
            echo $response;
        }
        break;
    case '/sale/list':
        if ($requestMethod == 'GET') {
            $response = $saleController->listSales();
            echo $response;
        }
        break;
    case '/sale/listByCustomer':
        if ($requestMethod == 'GET' && isset($_GET['customerId'])) {
            $customerId = $_GET['customerId'];
            $response = $saleController->listSalesByCustomer($customerId);
            echo $response;
        }
        break;
    case '/sale/show':
        if ($requestMethod == 'GET' && isset($_GET['id'])) {
            $response = $saleController->showSale($_GET['id']);
            echo $response;
        }
        break;
    case '/sale/update':
        if ($requestMethod == 'POST' && isset($_GET['id'])) {
            $response = $saleController->updateSale($_GET['id']);
            echo $response;
        }
        break;
    case '/sale/delete':
        if ($requestMethod == 'POST' && isset($_GET['id'])) {
            $response = $saleController->deleteSale($_GET['id']);
            echo $response;
        }
        break;

        //
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
