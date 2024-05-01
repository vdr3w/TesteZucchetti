<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use MyProject\Entity\Sale;
use MyProject\Repository\SaleRepository;

class SaleRepositoryTest extends TestCase
{
    private $entityManagerMock;
    private $saleRepository;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->saleRepository = new SaleRepository($this->entityManagerMock, new ClassMetadata(Sale::class));
    }

    public function testSaveSale_PersistsAndFlushesSale()
    {
        $sale = new Sale();
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($sale));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->saleRepository->save($sale);
    }

    public function testRemoveSale_RemovesAndFlushesSale()
    {
        $sale = new Sale();
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($sale));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->saleRepository->remove($sale);
    }
}
