<?php

use PHPUnit\Framework\TestCase;
use MyProject\Entity\PaymentMethod;

class PaymentMethodTest extends TestCase
{
    public function testSetNameWithValidName()
    {
        $paymentMethod = new PaymentMethod();
        $name = "Valid Payment Method Name";
        $paymentMethod->setName($name);
        $this->assertEquals($name, $paymentMethod->getName());
    }

    public function testSetNameWithEmptyNameThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("O nome da forma de pagamento não pode estar vazio.");

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName("");
    }

    public function testSetInstallmentsWithValidNumber()
    {
        $paymentMethod = new PaymentMethod();
        $installments = 5;
        $paymentMethod->setInstallments($installments);
        $this->assertEquals($installments, $paymentMethod->getInstallments());
    }

    public function testSetInstallmentsWithInvalidNumberThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("O número de parcelas deve ser ao menos 1.");

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setInstallments(0);
    }
}
