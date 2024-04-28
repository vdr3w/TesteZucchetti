<?php

namespace MyProject\Controller;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\Product;

class ProductController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProduct()
    {
        // Capturando o conteúdo JSON da requisição
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name'], $data['price'], $data['quantity'])) {
            http_response_code(400);
            return "Missing data for name, price or quantity.";
        }

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice((float) $data['price']);
        $product->setQuantity((int) $data['quantity']);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        http_response_code(201);
        return "Created Product with ID " . $product->getId() . "\n";
    }

    public function listProducts()
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $productList = [];

        foreach ($products as $product) {
            $productList[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $product->getQuantity()
            ];
        }

        header('Content-Type: application/json');
        return json_encode($productList);
    }

    public function showProduct($id)
    {
        $product = $this->entityManager->find(Product::class, $id);

        if (!$product) {
            http_response_code(404);
            return "No product found.";
        }

        header('Content-Type: application/json');
        return json_encode([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'quantity' => $product->getQuantity()
        ]);
    }

    public function updateProduct($id, $name, $price, $quantity)
    {
        $product = $this->entityManager->find(Product::class, $id);
        if (!$product) {
            http_response_code(404);
            return "Product $id does not exist.";
        }

        $product->setName($name);
        $product->setPrice((float) $price);
        $product->setQuantity((int) $quantity);

        $this->entityManager->flush();

        return "Product updated successfully.";
    }

    public function deleteProduct($id)
    {
        $product = $this->entityManager->find(Product::class, $id);

        if (!$product) {
            http_response_code(404);
            return "No product found.";
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return "Deleted product with ID $id.";
    }
}
