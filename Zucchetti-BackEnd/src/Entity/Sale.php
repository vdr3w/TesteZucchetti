<?php
namespace MyProject\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sales")]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: "Customer")]
    #[ORM\JoinColumn(name: "customer_id", referencedColumnName: "id")]
    private Customer $customer;

    #[ORM\ManyToMany(targetEntity: "Product")]
    #[ORM\JoinTable(name: "sale_products",
        joinColumns: [new ORM\JoinColumn(name: "sale_id", referencedColumnName: "id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    )]
    private $products;  // Coleção de produtos

    #[ORM\ManyToOne(targetEntity: "PaymentMethod")]
    #[ORM\JoinColumn(name: "payment_method_id", referencedColumnName: "id")]
    private PaymentMethod $paymentMethod;

    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getCustomer(): Customer {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void {
        $this->customer = $customer;
    }

    public function getProducts() {
        return $this->products;
    }

    public function addProduct(Product $product): void {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }
    }

    public function removeProduct(Product $product): void {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }
    }

    public function getPaymentMethod(): PaymentMethod {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): void {
        $this->paymentMethod = $paymentMethod;
    }
}
