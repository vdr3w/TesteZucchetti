<?php

namespace MyProject\Interface;

interface SaleServiceInterface {
    public function createSale(array $data): array;
    public function listSales(): array;
    public function listSalesByCustomer(int $customerId): array;
    public function showSale(int $id): array;
    public function updateSale(int $id, array $data): array;
    public function deleteSale(int $id): array;
}
