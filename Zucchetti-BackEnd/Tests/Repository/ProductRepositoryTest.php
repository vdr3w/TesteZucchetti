<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use MyProject\Repository\ProductRepository;
use MyProject\Entity\Product;

class ProductRepositoryTest extends TestCase
{
    private $entityManagerMock;
    private $repository;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $classMetadata = new ClassMetadata(Product::class);
        $this->repository = new ProductRepository($this->entityManagerMock, $classMetadata);
    }

    public function testSaveProduct_PersistsAndFlushesProduct()
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(99.99);
        $product->setQuantity(10);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($product));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->repository->save($product);
    }

    public function testRemoveProduct_RemovesAndFlushesProduct()
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(99.99);
        $product->setQuantity(10);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($product));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->repository->remove($product);
    }
}
