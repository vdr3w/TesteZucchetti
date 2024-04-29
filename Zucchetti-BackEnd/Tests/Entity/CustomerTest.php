<?php
use PHPUnit\Framework\TestCase;
use MyProject\Entity\Customer;

class CustomerTest extends TestCase
{
    public function testCanBeCreatedWithValidValues()
    {
        $customer = new Customer();
        $customer->setName("John Doe");
        $customer->setCpf("12345678901");
        $customer->setEmail("john.doe@example.com");
        $customer->setCep("12345-678");
        $customer->setAddress("123 Main St");

        $this->assertEquals("John Doe", $customer->getName());
        $this->assertEquals("12345678901", $customer->getCpf());
        $this->assertEquals("john.doe@example.com", $customer->getEmail());
        $this->assertEquals("12345-678", $customer->getCep());
        $this->assertEquals("123 Main St", $customer->getAddress());
    }

    public function testThrowsExceptionForInvalidName()
    {
        $this->expectException(InvalidArgumentException::class);
        $customer = new Customer();
        $customer->setName("");
    }

    public function testThrowsExceptionForInvalidCpf()
    {
        $this->expectException(InvalidArgumentException::class);
        $customer = new Customer();
        $customer->setCpf("invalid");
    }

    public function testThrowsExceptionForInvalidEmail()
    {
        $this->expectException(InvalidArgumentException::class);
        $customer = new Customer();
        $customer->setEmail("not-an-email");
    }

    public function testThrowsExceptionForInvalidCep()
    {
        $this->expectException(InvalidArgumentException::class);
        $customer = new Customer();
        $customer->setCep("bad-format");
    }

    public function testThrowsExceptionForInvalidAddress()
    {
        $this->expectException(InvalidArgumentException::class);
        $customer = new Customer();
        $customer->setAddress("");
    }
}
