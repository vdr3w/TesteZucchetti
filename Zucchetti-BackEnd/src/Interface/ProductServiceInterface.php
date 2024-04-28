<?php

namespace MyProject\Interface;

use MyProject\Entity\Product;

interface ProductServiceInterface {
    public function createProduct(array $data): Product;
    public function listProducts(): array;
    public function showProduct(int $id): ?Product;
    public function updateProduct(int $id, array $data): Product;
    public function deleteProduct(int $id): void;
}
