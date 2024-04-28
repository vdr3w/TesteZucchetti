<?php

namespace MyProject\Controller;

use MyProject\Service\ProductService;
use MyProject\Interface\ProductServiceInterface;  // Usando a interface ao invés da classe concreta.


class ProductController {
    private $productService;

    public function __construct(ProductServiceInterface $productService) {  // Injeção da interface
        $this->productService = $productService;
    }

    public function createProduct() {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->productService->createProduct($data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function listProducts() {
        $result = $this->productService->listProducts();
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function showProduct($id) {
        $result = $this->productService->showProduct($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function updateProduct($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->productService->updateProduct($id, $data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function deleteProduct($id) {
        $result = $this->productService->deleteProduct($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }
}
