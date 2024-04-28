<?php

use PHPUnit\Framework\TestCase;
use MyProject\Entity\Product;
use InvalidArgumentException;

class ProductTest extends TestCase
{
    public function testSetNameWithValidName()
    {
        $product = new Product();
        $name = "Valid Name";
        $product->setName($name);
        $this->assertEquals($name, $product->getName());
    }

    public function testSetNameWithEmptyNameThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("O nome do produto não pode estar vazio.");

        $product = new Product();
        $product->setName("");
    }

    public function testSetPriceWithPositiveValue()
    {
        $product = new Product();
        $price = 10.99;
        $product->setPrice($price);
        $this->assertEquals($price, $product->getPrice());
    }

    public function testSetPriceWithNonPositiveValueThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("O preço deve ser maior que zero.");

        $product = new Product();
        $product->setPrice(0);
    }

    public function testSetQuantityWithNonNegativeValue()
    {
        $product = new Product();
        $quantity = 5;
        $product->setQuantity($quantity);
        $this->assertEquals($quantity, $product->getQuantity());
    }

    public function testSetQuantityWithNegativeValueThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A quantidade não pode ser negativa.");

        $product = new Product();
        $product->setQuantity(-1);
    }
}
