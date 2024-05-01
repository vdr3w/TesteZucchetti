<?php

use PHPUnit\Framework\TestCase;
use MyProject\Entity\Sale;
use MyProject\Entity\Customer;
use MyProject\Entity\Product;
use MyProject\Entity\PaymentMethod;
use MyProject\Entity\SaleItem;

class SaleTest extends TestCase
{
    private $sale;

    protected function setUp(): void
    {
        $this->sale = new Sale();
    }

    public function testSetCustomer()
    {
        $customer = $this->createMock(Customer::class);
        $this->sale->setCustomer($customer);
        $this->assertSame($customer, $this->sale->getCustomer());
    }

    public function testAddProduct()
    {
        $product = $this->createMock(Product::class);
        $this->sale->addProduct($product);
        $this->assertTrue($this->sale->getProducts()->contains($product));
    }

    public function testAddProduct_DoesNotAddDuplicates()
    {
        $product = $this->createMock(Product::class);
        $this->sale->addProduct($product);
        $this->sale->addProduct($product);
        $this->assertCount(1, $this->sale->getProducts());
    }

    public function testRemoveProduct()
    {
        $product = $this->createMock(Product::class);
        $this->sale->addProduct($product);
        $this->sale->removeProduct($product);
        $this->assertFalse($this->sale->getProducts()->contains($product));
    }

    public function testRemoveProduct_OnlyRemovesSpecifiedProduct()
    {
        $product1 = $this->createMock(Product::class);
        $product2 = $this->createMock(Product::class);
        $this->sale->addProduct($product1);
        $this->sale->addProduct($product2);
        $this->sale->removeProduct($product1);
        $this->assertFalse($this->sale->getProducts()->contains($product1));
        $this->assertTrue($this->sale->getProducts()->contains($product2));
    }

    public function testSetPaymentMethod()
    {
        $paymentMethod = $this->createMock(PaymentMethod::class);
        $this->sale->setPaymentMethod($paymentMethod);
        $this->assertSame($paymentMethod, $this->sale->getPaymentMethod());
    }

    public function testAddItem()
    {
        $item = $this->createMock(SaleItem::class);
        $this->sale->addItem($item);
        $this->assertTrue($this->sale->getItems()->contains($item));
    }
}
