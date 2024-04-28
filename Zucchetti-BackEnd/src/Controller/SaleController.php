<?php

namespace MyProject\Controller;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\Sale;
use MyProject\Entity\Customer;
use MyProject\Entity\Product;
use MyProject\Entity\PaymentMethod;
use MyProject\Entity\SaleItem;

class SaleController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSale()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['customerId'], $data['paymentMethodId'], $data['items'])) {
            http_response_code(400);
            return "Missing data for customerId, paymentMethodId, or items.";
        }

        $customer = $this->entityManager->find(Customer::class, $data['customerId']);
        $paymentMethod = $this->entityManager->find(PaymentMethod::class, $data['paymentMethodId']);

        if (!$customer || !$paymentMethod) {
            http_response_code(404);
            return "Customer or Payment Method not found.";
        }

        $sale = new Sale();
        $sale->setCustomer($customer);
        $sale->setPaymentMethod($paymentMethod);
        $totalPrice = 0;

        foreach ($data['items'] as $item) {
            $product = $this->entityManager->find(Product::class, $item['productId']);
            if ($product && $product->getQuantity() >= $item['quantity']) {
                $saleItem = new SaleItem();
                $saleItem->setProduct($product);
                $saleItem->setQuantity($item['quantity']);
                $sale->addItem($saleItem);

                $product->setQuantity($product->getQuantity() - $item['quantity']);  // Decrementar estoque
                $totalPrice += $product->getPrice() * $item['quantity'];  // Calcula o total
            } else {
                http_response_code(404);
                return "Product {$item['productId']} not found or insufficient stock.";
            }
        }

        $this->entityManager->persist($sale);
        $this->entityManager->flush();

        http_response_code(201);
        return "Created Sale with ID " . $sale->getId() . " Total: $" . $totalPrice . "\n";
    }
}
