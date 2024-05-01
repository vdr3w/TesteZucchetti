<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use MyProject\Entity\Sale;
use MyProject\Entity\Customer;
use MyProject\Entity\Product;
use MyProject\Entity\PaymentMethod;
use MyProject\Entity\SaleItem;
use MyProject\Service\SaleService;

class SaleServiceTest extends TestCase
{
    private $entityManagerMock;
    private $saleService;
    private $saleRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->saleRepositoryMock = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $this->entityManagerMock->method('getRepository')
            ->with(Sale::class)
            ->willReturn($this->saleRepositoryMock);
        $this->saleService = new SaleService($this->entityManagerMock);
    }

    private function setIdUsingReflection($object, $id)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($object, $id);
    }

    public function testCreateSale_MissingData()
    {
        $data = [];
        $result = $this->saleService->createSale($data);
        $this->assertEquals(400, $result['httpCode']);
        $expectedBody = json_encode(['success' => false, 'error' => 'Dados faltando para customerId, paymentMethodId, items ou installments.']);
        $this->assertJsonStringEqualsJsonString($expectedBody, $result['body']);
    }

    public function testCreateSale_CustomerOrPaymentMethodNotFound()
    {
        $data = [
            'customerId' => 1,
            'paymentMethodId' => 1,
            'items' => [['productId' => 1, 'quantity' => 1]],
            'installments' => 1
        ];

        $this->entityManagerMock->method('find')
            ->willReturn(null);

        $result = $this->saleService->createSale($data);
        $this->assertEquals(404, $result['httpCode']);
        $expectedBody = json_encode(['success' => false, 'error' => 'Cliente ou método de pagamento não encontrado.']);
        $this->assertJsonStringEqualsJsonString($expectedBody, $result['body']);
    }

    public function testCreateSale_SuccessfulCreation()
    {
        $saleData = [
            'customerId' => 6,
            'paymentMethodId' => 1,
            'items' => [
                ['productId' => 7, 'quantity' => 33],
                ['productId' => 8, 'quantity' => 24]
            ],
            'installments' => 5
        ];

        $customer = $this->createMock(Customer::class);
        $paymentMethod = $this->createMock(PaymentMethod::class);
        $paymentMethod->method('getInstallments')->willReturn(10);

        $product1 = $this->createMock(Product::class);
        $product1->method('getQuantity')->willReturn(50);
        $product1->method('getPrice')->willReturn(2.00);

        $product2 = $this->createMock(Product::class);
        $product2->method('getQuantity')->willReturn(50);
        $product2->method('getPrice')->willReturn(3.00);

        $this->entityManagerMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturnCallback(function ($entityType, $id) use ($customer, $paymentMethod, $product1, $product2) {
                switch ($entityType) {
                    case Customer::class:
                        return $id == 6 ? $customer : null;
                    case PaymentMethod::class:
                        return $id == 1 ? $paymentMethod : null;
                    case Product::class:
                        return ($id == 7) ? $product1 : (($id == 8) ? $product2 : null);
                    default:
                        return null;
                }
            });

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($entity) {
                return $entity instanceof Sale;
            }));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->saleService->createSale($saleData);
        $totalPrice = 33 * 2.00 + 24 * 3.00;
        $installmentAmount = $totalPrice / 5;

        $expectedMessage = "Venda criada com sucesso com ID , Total: $" . number_format($totalPrice, 2);
        $this->assertEquals(201, $result['httpCode']);
        $responseBody = json_decode($result['body'], true);
        $this->assertEquals($expectedMessage, $responseBody['message']);
        $this->assertEquals(5, $responseBody['installments']);
        $this->assertEquals(number_format($installmentAmount, 2), $responseBody['installmentAmount']);
    }

    public function testListSales_ReturnsAllSales()
    {
        $sale = new Sale();
        $customer = new Customer();
        $paymentMethod = new PaymentMethod();
        $product = new Product();
        $item = new SaleItem();

        $reflectionSale = new \ReflectionClass($sale);
        $idProperty = $reflectionSale->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($sale, 1);

        $reflectionCustomer = new \ReflectionClass($customer);
        $idPropertyCustomer = $reflectionCustomer->getProperty('id');
        $idPropertyCustomer->setAccessible(true);
        $idPropertyCustomer->setValue($customer, 1);

        $reflectionPaymentMethod = new \ReflectionClass($paymentMethod);
        $idPropertyPaymentMethod = $reflectionPaymentMethod->getProperty('id');
        $idPropertyPaymentMethod->setAccessible(true);
        $idPropertyPaymentMethod->setValue($paymentMethod, 1);

        $reflectionProduct = new \ReflectionClass($product);
        $idPropertyProduct = $reflectionProduct->getProperty('id');
        $idPropertyProduct->setAccessible(true);
        $idPropertyProduct->setValue($product, 100);

        $sale->setCustomer($customer);
        $sale->setPaymentMethod($paymentMethod);
        $item->setProduct($product);
        $item->setQuantity(10);
        $sale->addItem($item);

        $this->saleRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$sale]);

        $result = $this->saleService->listSales();

        $expectedResult = json_encode([
            [
                'id' => $sale->getId(),
                'customer' => $customer->getId(),
                'paymentMethod' => $paymentMethod->getId(),
                'items' => [
                    [
                        'productId' => $product->getId(),
                        'quantity' => 10
                    ]
                ]
            ]
        ]);

        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString($expectedResult, $result['body']);
    }

    public function testListSalesByCustomer_ReturnsSalesForGivenCustomer()
    {
        $customerId = 1;
        $customer = new Customer();
        $this->setIdUsingReflection($customer, $customerId);
        $sale = new Sale();
        $this->setIdUsingReflection($sale, 10);
        $sale->setCustomer($customer);
        $paymentMethod = new PaymentMethod();
        $this->setIdUsingReflection($paymentMethod, 5);
        $product = new Product();
        $this->setIdUsingReflection($product, 101);
        $item = new SaleItem();
        $item->setProduct($product);
        $item->setQuantity(2);
        $sale->addItem($item);
        $sale->setPaymentMethod($paymentMethod);

        $this->entityManagerMock->method('find')
            ->with(Customer::class, $customerId)
            ->willReturn($customer);

        $this->saleRepositoryMock->method('findBy')
            ->with(['customer' => $customer])
            ->willReturn([$sale]);

        $result = $this->saleService->listSalesByCustomer($customerId);

        $expectedResult = json_encode([
            [
                'id' => $sale->getId(),
                'customer' => $customer->getId(),
                'paymentMethod' => $paymentMethod->getId(),
                'items' => [
                    [
                        'productId' => $product->getId(),
                        'quantity' => 2
                    ]
                ]
            ]
        ]);

        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString($expectedResult, $result['body']);
    }

    public function testListSales_HandlesException()
    {
        $this->saleRepositoryMock->method('findAll')
            ->will($this->throwException(new \Exception("Erro ao acessar o banco de dados")));

        $result = $this->saleService->listSales();

        $this->assertEquals(500, $result['httpCode']);
        $this->assertStringContainsString('Erro ao listar vendas', $result['body']);
    }

    public function testShowSale_Successful()
    {
        $saleId = 1;
        $sale = new Sale();
        $this->setIdUsingReflection($sale, $saleId);
        $customer = new Customer();
        $this->setIdUsingReflection($customer, 1);
        $paymentMethod = new PaymentMethod();
        $this->setIdUsingReflection($paymentMethod, 2);
        $product = new Product();
        $this->setIdUsingReflection($product, 101);
        $item = new SaleItem();
        $item->setProduct($product);
        $item->setQuantity(2);
        $sale->addItem($item);
        $sale->setCustomer($customer);
        $sale->setPaymentMethod($paymentMethod);

        $this->saleRepositoryMock->expects($this->once())
            ->method('find')
            ->with($saleId)
            ->willReturn($sale);

        $result = $this->saleService->showSale($saleId);

        $expectedResult = json_encode([
            'id' => $sale->getId(),
            'customer' => $customer->getId(),
            'paymentMethod' => $paymentMethod->getId(),
            'items' => [
                [
                    'productId' => $product->getId(),
                    'quantity' => 2
                ]
            ]
        ]);

        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString($expectedResult, $result['body']);
    }

    public function testUpdateSale_SuccessfulUpdate()
    {
        $saleId = 1;
        $updatedData = [
            'customerId' => 1,
            'paymentMethodId' => 2,
            'items' => [
                ['productId' => 101, 'quantity' => 3]
            ]
        ];

        $customer = $this->createMock(Customer::class);
        $customer->method('getId')->willReturn(1);
        $paymentMethod = $this->createMock(PaymentMethod::class);
        $paymentMethod->method('getId')->willReturn(2);
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(101);
        $product->method('getQuantity')->willReturn(10);

        $itemsCollection = new \Doctrine\Common\Collections\ArrayCollection();
        $sale = $this->createMock(Sale::class);
        $sale->method('getId')->willReturn($saleId);
        $sale->method('getCustomer')->willReturn($customer);
        $sale->method('getPaymentMethod')->willReturn($paymentMethod);
        $sale->method('getItems')->willReturn($itemsCollection);

        $this->entityManagerMock->method('find')
            ->willReturnCallback(function ($entityType, $id) use ($customer, $paymentMethod, $product, $sale) {
                switch ($entityType) {
                    case Customer::class:
                        return $id == 1 ? $customer : null;
                    case PaymentMethod::class:
                        return $id == 2 ? $paymentMethod : null;
                    case Product::class:
                        return $id == 101 ? $product : null;
                    case Sale::class:
                        return $id == $saleId ? $sale : null;
                    default:
                        return null;
                }
            });

        $this->saleRepositoryMock->method('find')
            ->with($saleId)
            ->willReturn($sale);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->saleService->updateSale($saleId, $updatedData);

        $this->assertEquals(200, $result['httpCode']);
        $this->assertStringContainsString('Venda atualizada com sucesso', $result['body']);
    }

    public function testUpdateSale_CustomerNotFound()
    {
        $saleId = 1;
        $updatedData = [
            'customerId' => 99,
            'paymentMethodId' => 2,
            'items' => [['productId' => 101, 'quantity' => 3]]
        ];

        $sale = $this->createMock(Sale::class);
        $this->saleRepositoryMock->method('find')->with($saleId)->willReturn($sale);
        $this->entityManagerMock->method('find')->willReturnMap([
            [Customer::class, 99, null],
        ]);

        $result = $this->saleService->updateSale($saleId, $updatedData);

        $expectedResult = json_encode(['success' => false, 'error' => 'Cliente não encontrado.']);
        $this->assertEquals(404, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString($expectedResult, $result['body']);
    }

    public function testDeleteSale_SuccessfulDeletion()
    {
        $saleId = 1;
        $sale = new Sale();
        $this->setIdUsingReflection($sale, $saleId);

        $this->saleRepositoryMock->expects($this->once())
            ->method('find')
            ->with($saleId)
            ->willReturn($sale);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($sale));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->saleService->deleteSale($saleId);

        $expectedResult = json_encode(['success' => true, 'message' => 'Venda excluída com sucesso com ID ' . $saleId]);

        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString($expectedResult, $result['body']);
    }

    public function testDeleteSale_SaleNotFound()
    {
        $saleId = 1;

        $this->saleRepositoryMock->method('find')
            ->with($saleId)
            ->willReturn(null);

        $result = $this->saleService->deleteSale($saleId);

        $expectedResult = json_encode(['success' => false, 'error' => 'Venda não encontrada.']);
        $this->assertEquals(404, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString($expectedResult, $result['body']);
    }
}
