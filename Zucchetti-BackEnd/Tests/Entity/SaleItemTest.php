<?php

use PHPUnit\Framework\TestCase;
use MyProject\Entity\SaleItem;
use MyProject\Entity\Sale;
use MyProject\Entity\Product;

class SaleItemTest extends TestCase
{
    public function testSetAndGetSale()
    {
        $saleItem = new SaleItem();
        $sale = $this->createMock(Sale::class);
        $saleItem->setSale($sale);
        $this->assertSame($sale, $saleItem->getSale(), "A venda associada ao item não é a mesma que foi definida.");
    }

    public function testSetAndGetProduct()
    {
        $saleItem = new SaleItem();
        $product = $this->createMock(Product::class);
        $saleItem->setProduct($product);
        $this->assertSame($product, $saleItem->getProduct(), "O produto associado ao item não é o mesmo que foi definido.");
    }

    public function testSetAndGetQuantity()
    {
        $saleItem = new SaleItem();
        $quantity = 10;
        $saleItem->setQuantity($quantity);
        $this->assertEquals($quantity, $saleItem->getQuantity(), "A quantidade definida no item não é a mesma que foi recuperada.");
    }

    public function testSetQuantityAcceptsValues()
    {
        $saleItem = new SaleItem();
        $validQuantity = 10;
        $invalidQuantity = -1;
        $saleItem->setQuantity($validQuantity);
        $this->assertEquals($validQuantity, $saleItem->getQuantity(), "A quantidade deve ser $validQuantity.");

        $saleItem->setQuantity($invalidQuantity);
        $this->assertEquals($invalidQuantity, $saleItem->getQuantity(), "A quantidade deve ser $invalidQuantity mesmo que seja um valor negativo.");
    }
}
