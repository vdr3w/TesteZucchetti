<?php

namespace MyProject\Controller;

use MyProject\Interface\CustomerServiceInterface;

class CustomerController
{
    private $customerService;

    public function __construct(CustomerServiceInterface $customerService) {
        $this->customerService = $customerService;
    }

    public function createCustomer() {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->customerService->createCustomer($data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function listCustomers() {
        $result = $this->customerService->listCustomers();
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function showCustomer($id) {
        $result = $this->customerService->showCustomer($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function updateCustomer($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->customerService->updateCustomer($id, $data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function deleteCustomer($id) {
        $result = $this->customerService->deleteCustomer($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }
}
