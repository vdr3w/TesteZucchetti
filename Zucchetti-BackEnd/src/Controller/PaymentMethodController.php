<?php

namespace MyProject\Controller;

use MyProject\Interface\PaymentMethodServiceInterface;

class PaymentMethodController {
    private $paymentMethodService;

    public function __construct(PaymentMethodServiceInterface $paymentMethodService) {
        $this->paymentMethodService = $paymentMethodService;
    }

    public function createPaymentMethod() {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->paymentMethodService->createPaymentMethod($data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function listPaymentMethods() {
        $result = $this->paymentMethodService->listPaymentMethods();
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function showPaymentMethod($id) {
        $result = $this->paymentMethodService->showPaymentMethod($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function updatePaymentMethod($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->paymentMethodService->updatePaymentMethod($id, $data);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }

    public function deletePaymentMethod($id) {
        $result = $this->paymentMethodService->deletePaymentMethod($id);
        http_response_code($result['httpCode']);
        echo $result['body'];
    }
}
