<?php

namespace MyProject\Controller;

use MyProject\Interface\SaleServiceInterface;

class SaleController {
    private $saleService;

    public function __construct(SaleServiceInterface $saleService) {
        $this->saleService = $saleService;
    }

    public function createSale() {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->saleService->createSale($data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function listSales() {
        $result = $this->saleService->listSales();
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function listSalesByCustomer($customerId) {
        $result = $this->saleService->listSalesByCustomer($customerId);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function showSale($id) {
        $result = $this->saleService->showSale($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function updateSale($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->saleService->updateSale($id, $data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function deleteSale($id) {
        $result = $this->saleService->deleteSale($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }
}
