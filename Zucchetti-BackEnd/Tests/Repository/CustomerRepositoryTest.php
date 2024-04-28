<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use MyProject\Entity\Customer;
use MyProject\Repository\CustomerRepository;

class CustomerRepositoryTest extends TestCase
{
    private $entityManagerMock;
    private $customerRepository;
    private $customerMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->customerRepository = new CustomerRepository($this->entityManagerMock, new Doctrine\ORM\Mapping\ClassMetadata(Customer::class));
        $this->customerMock = $this->createMock(Customer::class);
    }

    public function testSaveProduct_PersistsAndFlushesProduct()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->customerMock);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->customerRepository->save($this->customerMock);
    }

    public function testRemoveProduct_RemovesAndFlushesProduct()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->customerMock);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->customerRepository->remove($this->customerMock);
    }
}
