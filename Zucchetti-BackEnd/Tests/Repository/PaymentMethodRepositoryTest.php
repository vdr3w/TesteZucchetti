<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use MyProject\Entity\PaymentMethod;
use MyProject\Repository\PaymentMethodRepository;

class PaymentMethodRepositoryTest extends TestCase
{
    private $entityManagerMock;
    private $repository;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->repository = new PaymentMethodRepository($this->entityManagerMock, new Doctrine\ORM\Mapping\ClassMetadata(PaymentMethod::class));
    }

    public function testSavePaymentMethod_PersistsAndFlushesProduct()
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName("Credit Card");
        $paymentMethod->setInstallments(3);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($paymentMethod);
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->repository->save($paymentMethod);
    }

    public function testRemovePaymentMethod_RemovesAndFlushesProduct()
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName("Debit Card");
        $paymentMethod->setInstallments(1);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($paymentMethod);
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->repository->remove($paymentMethod);
    }
}
