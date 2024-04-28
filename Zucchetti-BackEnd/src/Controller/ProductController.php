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
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name'], $data['price'], $data['quantity'])) {
            http_response_code(400);
            return "Missing data for name, price or quantity.";
        }

        try {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice((float) $data['price']);
            $product->setQuantity((int) $data['quantity']);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Produto criado com sucesso com ID ' . $product->getId()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao criar produto: ' . $e->getMessage()]);
        }
    }

    public function listProducts()
    {
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

            header('Content-Type: application/json');
            echo json_encode($productList);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao listar produtos: ' . $e->getMessage()]);
        }
    }

    public function showProduct($id)
    {
        try {
            $product = $this->entityManager->find(Product::class, $id);

            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $product->getQuantity()
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao exibir produto: ' . $e->getMessage()]);
        }
    }

    public function updateProduct($id, $name, $price, $quantity)
    {
        try {
            $product = $this->entityManager->find(Product::class, $id);
            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => "Produto $id não existe."]);
                return;
            }

            $product->setName($name);
            $product->setPrice((float) $price);
            $product->setQuantity((int) $quantity);

            $this->entityManager->flush();

            echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso.']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar produto: ' . $e->getMessage()]);
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = $this->entityManager->find(Product::class, $id);

            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
                return;
            }

            $this->entityManager->remove($product);
            $this->entityManager->flush();

            echo json_encode(['success' => true, 'message' => 'Produto excluído com sucesso.']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir produto: ' . $e->getMessage()]);
        }
    }
}
