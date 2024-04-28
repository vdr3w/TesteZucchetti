<?php
namespace MyProject\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sale_items")]
class SaleItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: "Sale", inversedBy: "items")]
    private Sale $sale;

    #[ORM\ManyToOne(targetEntity: "Product")]
    private Product $product;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function getId(): ?int {
        return $this->id;
    }

    public function getSale(): Sale {
        return $this->sale;
    }

    public function setSale(Sale $sale): void {
        $this->sale = $sale;
    }

    public function getProduct(): Product {
        return $this->product;
    }

    public function setProduct(Product $product): void {
        $this->product = $product;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }
}
