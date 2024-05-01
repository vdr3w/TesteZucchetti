<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use MyProject\Entity\Customer;
use MyProject\Service\CustomerService;

class CustomerServiceTest extends TestCase
{
    private $entityManagerMock;
    private $customerService;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->customerService = new CustomerService($this->entityManagerMock);
    }

    public function testCreateCustomer_Success()
    {
        $customerData = [
            'name' => 'John Doe',
            'cpf' => '12345678901',
            'email' => 'john.doe@example.com',
            'cep' => '12345-678',
            'address' => '123 Main St'
        ];

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($customer) {
                return $customer instanceof Customer &&
                    $customer->getName() === 'John Doe' &&
                    $customer->getCpf() === '12345678901' &&
                    $customer->getEmail() === 'john.doe@example.com' &&
                    $customer->getCep() === '12345-678' &&
                    $customer->getAddress() === '123 Main St';
            }));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->customerService->createCustomer($customerData);
        $this->assertEquals(201, $result['httpCode']);
        $this->assertStringContainsString('Cliente criado com sucesso', $result['body']);
    }

    public function testCreateCustomer_FailureMissingData()
    {
        $customerData = ['name' => 'John Doe'];

        $result = $this->customerService->createCustomer($customerData);
        $this->assertEquals(400, $result['httpCode']);
        $this->assertJson($result['body'], "The body should be a valid JSON string.");

        $expectedJson = json_encode(["success" => false, "message" => "Missing data for name, cpf, email, cep, or address."]);
        $this->assertJsonStringEqualsJsonString($expectedJson, $result['body']);
    }

    public function testCreateCustomer_ExceptionDuringPersistence()
    {
        $customerData = [
            'name' => 'John Doe',
            'cpf' => '12345678901',
            'email' => 'john.doe@example.com',
            'cep' => '12345-678',
            'address' => '123 Main St'
        ];

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->will($this->throwException(new \Exception("Database Error")));

        $result = $this->customerService->createCustomer($customerData);
        $this->assertEquals(500, $result['httpCode']);
        $expectedJson = json_encode(['success' => false, 'error' => 'Erro ao criar cliente: Database Error']);
        $this->assertJsonStringEqualsJsonString($expectedJson, $result['body']);
    }

    public function testListCustomers_ReturnsAllCustomers()
    {
        $customerMock = new Customer();
        $reflectionClass = new \ReflectionClass($customerMock);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($customerMock, 1);
        $customerMock->setName('John Doe');
        $customerMock->setCpf('12345678901');
        $customerMock->setEmail('john.doe@example.com');
        $customerMock->setCep('12345-678');
        $customerMock->setAddress('123 Main St');

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(Customer::class)
            ->willReturnCallback(function () use ($customerMock) {
                $mockRepo = $this->createMock(\Doctrine\Persistence\ObjectRepository::class);
                $mockRepo->method('findAll')->willReturn([$customerMock]);
                return $mockRepo;
            });

        $result = $this->customerService->listCustomers();
        $this->assertEquals(200, $result['httpCode']);
        $this->assertNotEmpty($result['body']);
    }

    public function testListCustomers_HandlesException()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(Customer::class)
            ->will($this->throwException(new \Exception("Database Error")));

        $result = $this->customerService->listCustomers();
        $this->assertEquals(500, $result['httpCode']);
        $expectedJson = json_encode(['success' => false, 'error' => 'Erro ao listar clientes: Database Error']);
        $this->assertJsonStringEqualsJsonString($expectedJson, $result['body']);
    }

    public function testShowCustomer_CustomerFound()
    {
        $customer = new Customer();
        $reflectionClass = new \ReflectionClass($customer);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($customer, 1);
        $customer->setName('John Doe');
        $customer->setCpf('12345678901');
        $customer->setEmail('john.doe@example.com');
        $customer->setCep('12345-678');
        $customer->setAddress('123 Main St');

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Customer::class, 1)
            ->willReturn($customer);

        $result = $this->customerService->showCustomer(1);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertNotEmpty($result['body']);
    }

    public function testShowCustomer_CustomerNotFound()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Customer::class, 999)
            ->willReturn(null);

        $result = $this->customerService->showCustomer(999);
        $this->assertEquals(404, $result['httpCode']);
        $expectedJson = json_encode(['success' => false, 'error' => 'Cliente não encontrado.']);
        $this->assertJsonStringEqualsJsonString($expectedJson, $result['body']);
    }

    public function testUpdateCustomer_SuccessfulUpdate()
    {
        $customer = new Customer();
        $reflectionClass = new \ReflectionClass($customer);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($customer, 1);
        $customer->setName('John Doe');

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Customer::class, 1)
            ->willReturn($customer);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->customerService->updateCustomer(1, ['name' => 'Jane Doe']);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true, 'message' => 'Cliente atualizado com sucesso.']), $result['body']);
    }

    public function testUpdateCustomer_CustomerNotFound()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Customer::class, 999)
            ->willReturn(null);

        $result = $this->customerService->updateCustomer(999, ['name' => 'Jane Doe']);
        $this->assertEquals(404, $result['httpCode']);
        $expectedJson = json_encode(['success' => false, 'error' => "Cliente 999 não encontrado."]);
        $this->assertJsonStringEqualsJsonString($expectedJson, $result['body']);
    }

    public function testDeleteCustomer_SuccessfulDeletion()
    {
        $customer = new Customer();
        $reflectionClass = new \ReflectionClass($customer);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($customer, 1);

        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Customer::class, 1)
            ->willReturn($customer);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($customer);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->customerService->deleteCustomer(1);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso.']), $result['body']);
    }

    public function testDeleteCustomer_CustomerNotFound()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('find')
            ->with(Customer::class, 999)
            ->willReturn(null);

        $result = $this->customerService->deleteCustomer(999);
        $this->assertEquals(404, $result['httpCode']);
        $expectedJson = json_encode(['success' => false, 'error' => 'Cliente não encontrado.']);
        $this->assertJsonStringEqualsJsonString($expectedJson, $result['body']);
    }
}