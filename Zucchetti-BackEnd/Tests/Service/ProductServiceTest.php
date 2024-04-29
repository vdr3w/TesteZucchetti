<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use MyProject\Entity\Product;
use MyProject\Repository\ProductRepository;
use MyProject\Service\ProductService;

class ProductServiceTest extends TestCase
{
    private $entityManagerMock;
    private $productRepositoryMock;
    private $productService;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->entityManagerMock->method('getRepository')->willReturn($this->productRepositoryMock);
        $this->productService = new ProductService($this->entityManagerMock);
    }

    public function testCreateProduct_CreatesAndPersistsProduct()
    {
        $productData = ['name' => 'New Product', 'price' => 50.00, 'quantity' => 15];

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($product) {
                return $product->getName() === 'New Product' && $product->getPrice() == 50.00 && $product->getQuantity() == 15;
            }));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->productService = new ProductService($this->entityManagerMock);

        $result = $this->productService->createProduct($productData);
        $this->assertEquals(201, $result['httpCode'], "Expected HTTP status 201, got " . $result['httpCode']);
        $this->assertStringContainsString('Produto criado com sucesso', $result['body']);
    }

    public function testListProducts_ReturnsAllProducts()
    {
        $productMock = new Product();
        $productMock->setName('Sample Product');
        $productMock->setPrice(99.99);
        $productMock->setQuantity(5);

        $this->productRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$productMock]);

        $result = $this->productService->listProducts();
        $this->assertEquals(200, $result['httpCode']);
        $this->assertNotEmpty($result['body']);
    }

    public function testShowProduct_ReturnsProductDetails()
    {
        $productId = 1;
        $product = new Product();
        $product->setName('Sample Product');
        $product->setPrice(99.99);
        $product->setQuantity(5);

        $reflectionClass = new \ReflectionClass($product);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($product, $productId);

        $this->entityManagerMock->method('find')
            ->willReturnCallback(function ($entityType, $id) use ($product, $productId) {
                if ($entityType === Product::class && $id == $productId) {
                    return $product;
                }
                return null;
            });

        $result = $this->productService->showProduct($productId);
        $this->assertEquals(200, $result['httpCode'], "Expected HTTP status 200, got " . $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(json_encode([
            'id' => $productId,
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'quantity' => $product->getQuantity()
        ]), $result['body']);
    }

    public function testShowProduct_ProductNotFound()
    {
        $productId = 1;

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Product::class, $this->equalTo($productId))
            ->willReturn(null);

        $this->productService = new ProductService($this->entityManagerMock);

        $result = $this->productService->showProduct($productId);
        $this->assertEquals(404, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString('{"success":false,"error":"Produto não encontrado."}', $result['body']);
    }

    public function testUpdateProduct_UpdatesExistingProduct()
    {
        $productId = 1;
        $productData = ['name' => 'Updated Product', 'price' => 150.00, 'quantity' => 20];

        $productMock = new Product();
        $productMock->setName('Old Product');
        $productMock->setPrice(100.00);
        $productMock->setQuantity(10);

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Product::class, $productId)
            ->willReturn($productMock);

        $this->entityManagerMock->expects($this->once())->method('flush');

        $this->productService = new ProductService($this->entityManagerMock);

        $result = $this->productService->updateProduct($productId, $productData);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso.']), $result['body']);
    }

    public function testUpdateProduct_ProductNotFound()
    {
        $productId = 1;
        $productData = ['name' => 'Updated Product', 'price' => 150.00, 'quantity' => 20];

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Product::class, $productId)
            ->willReturn(null);

        $this->productService = new ProductService($this->entityManagerMock);

        $result = $this->productService->updateProduct($productId, $productData);
        $this->assertEquals(404, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'error' => "Produto $productId não existe."]),
            $result['body']
        );
    }

    public function testDeleteProduct_DeletesProduct()
    {
        $productId = 1;
        $productMock = new Product();

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Product::class, $productId)
            ->willReturn($productMock);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($productMock));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->productService = new ProductService($this->entityManagerMock);

        $result = $this->productService->deleteProduct($productId);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true, 'message' => 'Produto excluído com sucesso.']),
            $result['body']
        );
    }

    public function testDeleteProduct_ProductNotFound()
    {
        $productId = 1;

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Product::class, $productId)
            ->willReturn(null);

        $result = $this->productService->deleteProduct($productId);

        $this->assertEquals(404, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'error' => 'Produto não encontrado.']),
            $result['body']
        );
    }
}
