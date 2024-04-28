<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use MyProject\Entity\PaymentMethod;
use MyProject\Service\PaymentMethodService;

class PaymentMethodServiceTest extends TestCase
{
    private $entityManagerMock;
    private $paymentMethodService;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->paymentMethodService = new PaymentMethodService($this->entityManagerMock);
    }

    public function testCreatePaymentMethod_CreatesAndPersistsPaymentMethod()
    {
        $paymentMethodData = ['name' => 'Credit Card', 'installments' => 12];

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($paymentMethod) {
                $reflectionClass = new \ReflectionClass($paymentMethod);
                $idProperty = $reflectionClass->getProperty('id');
                $idProperty->setAccessible(true);
                $idProperty->setValue($paymentMethod, 1);  // Simulate setting an ID
                return $paymentMethod->getName() === 'Credit Card' && $paymentMethod->getInstallments() == 12;
            }));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->paymentMethodService->createPaymentMethod($paymentMethodData);
        $this->assertEquals(201, $result['httpCode'], "Expected HTTP status 201, got " . $result['httpCode']);
        $expectedBody = json_encode(['success' => true, 'message' => 'Método de pagamento criado com sucesso com ID 1']);
        $this->assertJsonStringEqualsJsonString($expectedBody, $result['body']);
    }

    public function testListPaymentMethods_ReturnsAllPaymentMethods()
    {
        $paymentMethodMock = new PaymentMethod();
        $paymentMethodMock->setName('Debit Card');
        $paymentMethodMock->setInstallments(1);

        $this->entityManagerMock->method('getRepository')
            ->willReturnCallback(function () use ($paymentMethodMock) {
                $mockRepo = $this->createMock(\Doctrine\Persistence\ObjectRepository::class);
                $mockRepo->method('findAll')->willReturn([$paymentMethodMock]);
                return $mockRepo;
            });

        $result = $this->paymentMethodService->listPaymentMethods();
        $this->assertEquals(200, $result['httpCode']);
        $this->assertNotEmpty($result['body']);
    }

    public function testShowPaymentMethod_ReturnsPaymentMethodDetails()
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName('Credit Card');
        $paymentMethod->setInstallments(12);

        $reflectionClass = new \ReflectionClass($paymentMethod);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($paymentMethod, 1);

        $this->entityManagerMock->method('find')
            ->willReturnCallback(function ($entityType, $id) use ($paymentMethod) {
                if ($entityType === PaymentMethod::class && $id == 1) {
                    return $paymentMethod;
                }
                return null;
            });

        $result = $this->paymentMethodService->showPaymentMethod(1);
        $this->assertEquals(200, $result['httpCode'], "Expected HTTP status 200, got " . $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(json_encode([
            'id' => 1,
            'name' => $paymentMethod->getName(),
            'installments' => $paymentMethod->getInstallments()
        ]), $result['body']);
    }

    public function testShowPaymentMethod_PaymentMethodNotFound()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(PaymentMethod::class, $this->equalTo(999))
            ->willReturn(null);

        $result = $this->paymentMethodService->showPaymentMethod(999);
        $this->assertEquals(404, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString('{"success":false,"error":"Método de pagamento não encontrado."}', $result['body']);
    }

    public function testUpdatePaymentMethod_UpdatesExistingPaymentMethod()
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName('Credit Card');
        $paymentMethod->setInstallments(12);

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(PaymentMethod::class, 1)
            ->willReturn($paymentMethod);

        $this->entityManagerMock->expects($this->once())->method('flush');

        $result = $this->paymentMethodService->updatePaymentMethod(1, ['name' => 'Updated Credit Card', 'installments' => 24]);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true, 'message' => 'Método de pagamento atualizado com sucesso.']), $result['body']);
    }

    public function testDeletePaymentMethod_DeletesPaymentMethod()
    {
        $paymentMethod = new PaymentMethod();

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(PaymentMethod::class, 1)
            ->willReturn($paymentMethod);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($paymentMethod));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->paymentMethodService->deletePaymentMethod(1);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true, 'message' => 'Método de pagamento excluído com sucesso.']),
            $result['body']
        );
    }

    public function testDeletePaymentMethod_PaymentMethodNotFound()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(PaymentMethod::class, 999)
            ->willReturn(null);

        $result = $this->paymentMethodService->deletePaymentMethod(999);
        $this->assertEquals(404, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'error' => 'Método de pagamento não encontrado.']),
            $result['body']
        );
    }
}
