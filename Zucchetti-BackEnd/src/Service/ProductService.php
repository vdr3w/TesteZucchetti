<?php

namespace MyProject\Service;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\Product;

class ProductService {
    private $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function createProduct(array $data) {
        if (!isset($data['name'], $data['price'], $data['quantity'])) {
            return ['httpCode' => 400, 'body' => "Missing data for name, price or quantity."];
        }

        try {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice((float) $data['price']);
            $product->setQuantity((int) $data['quantity']);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return ['httpCode' => 201, 'body' => json_encode(['success' => true, 'message' => 'Produto criado com sucesso com ID ' . $product->getId()])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao criar produto: ' . $e->getMessage()])];
        }
    }

    public function listProducts() {
        try {
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

            return ['httpCode' => 200, 'body' => json_encode($productList)];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao listar produtos: ' . $e->getMessage()])];
        }
    }

    public function showProduct($id) {
        try {
            $product = $this->entityManager->find(Product::class, $id);

            if (!$product) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Produto não encontrado.'])];
            }

            return ['httpCode' => 200, 'body' => json_encode([
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $product->getQuantity()
            ])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao exibir produto: ' . $e->getMessage()])];
        }
    }

    public function updateProduct($id, array $data) {
        try {
            $product = $this->entityManager->find(Product::class, $id);
            if (!$product) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => "Produto $id não existe."])];
            }

            $product->setName($data['name']);
            $product->setPrice((float) $data['price']);
            $product->setQuantity((int) $data['quantity']);

            $this->entityManager->flush();

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso.'])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao atualizar produto: ' . $e->getMessage()])];
        }
    }

    public function deleteProduct($id) {
        try {
            $product = $this->entityManager->find(Product::class, $id);

            if (!$product) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Produto não encontrado.'])];
            }

            $this->entityManager->remove($product);
            $this->entityManager->flush();

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Produto excluído com sucesso.'])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao excluir produto: ' . $e->getMessage()])];
        }
    }
}
