<?php

namespace MyProject\Interface;

interface CustomerServiceInterface {
    public function createCustomer(array $data): array;
    public function listCustomers(): array;
    public function showCustomer(int $id): array;
    public function updateCustomer(int $id, array $data): array;
    public function deleteCustomer(int $id): array;
}
