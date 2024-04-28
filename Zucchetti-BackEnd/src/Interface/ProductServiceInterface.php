<?php
namespace MyProject\Interface;

interface ProductServiceInterface {
    public function createProduct(array $data): array;
    public function listProducts(): array;
    public function showProduct(int $id): array;
    public function updateProduct(int $id, array $data): array;
    public function deleteProduct(int $id): array;
}
