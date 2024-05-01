<?php

namespace MyProject\Service;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\Product;
use MyProject\Interface\ProductServiceInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class ProductService implements ProductServiceInterface
{
    private $entityManager;
    private $cache;

    public function __construct(EntityManager $entityManager, FilesystemAdapter $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    private function clearProductCache()
    {
        $this->cache->delete('product_list_cache');
    }

    public function createProduct(array $data): array
    {
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

            $this->cache->delete('product_list_cache');

            return ['httpCode' => 201, 'body' => json_encode(['success' => true, 'message' => 'Produto criado com sucesso com ID ' . $product->getId()])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao criar produto: ' . $e->getMessage()])];
        }
    }

    public function listProducts(): array
    {
        $cacheKey = 'product_list_cache';

        $cachedData = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(3600);
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
            return json_encode($productList);
        });

        if ($cachedData === false) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao codificar dados dos produtos.'])];
        }

        return ['httpCode' => 200, 'body' => $cachedData];
    }

    public function showProduct(int $id): array
    {
        $cacheKey = "product_$id";

        $cachedData = $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            $product = $this->entityManager->find(Product::class, $id);
            if (!$product) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Produto não encontrado.'])];
            }
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $product->getQuantity()
            ];
        });

        if ($cachedData === false) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao recuperar dados do cache.'])];
        }

        if (!is_array($cachedData)) {
            $cachedData = json_decode($cachedData, true);
        }

        return ['httpCode' => 200, 'body' => json_encode($cachedData)];
    }


    public function updateProduct(int $id, array $data): array
    {
        try {
            $product = $this->entityManager->find(Product::class, $id);
            if (!$product) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => "Produto $id não existe."])];
            }

            $product->setName($data['name']);
            $product->setPrice((float) $data['price']);
            $product->setQuantity((int) $data['quantity']);

            $this->entityManager->flush();
            $this->clearProductCache();

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso.'])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao atualizar produto: ' . $e->getMessage()])];
        }
    }

    public function deleteProduct(int $id): array
    {
        try {
            $product = $this->entityManager->find(Product::class, $id);

            if (!$product) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Produto não encontrado.'])];
            }

            $this->entityManager->remove($product);
            $this->entityManager->flush();
            $this->clearProductCache();

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Produto excluído com sucesso.'])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao excluir produto: ' . $e->getMessage()])];
        }
    }
}
