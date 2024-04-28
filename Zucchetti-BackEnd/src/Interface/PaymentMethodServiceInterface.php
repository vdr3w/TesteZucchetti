<?php

namespace MyProject\Interface;

interface PaymentMethodServiceInterface {
    public function createPaymentMethod(array $data): array;
    public function listPaymentMethods(): array;
    public function showPaymentMethod(int $id): array;
    public function updatePaymentMethod(int $id, array $data): array;
    public function deletePaymentMethod(int $id): array;
}
