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

                $product->setQuantity($product->getQuantity() - $item['quantity']);
                $totalPrice += $product->getPrice() * $item['quantity'];
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

    public function listSales()
    {
        $sales = $this->entityManager->getRepository(Sale::class)->findAll();
        $salesList = [];

        foreach ($sales as $sale) {
            $itemsList = array_map(function ($item) {
                return [
                    'productId' => $item->getProduct()->getId(),
                    'quantity' => $item->getQuantity()
                ];
            }, $sale->getItems()->toArray());

            $salesList[] = [
                'id' => $sale->getId(),
                'customer' => $sale->getCustomer()->getId(),
                'paymentMethod' => $sale->getPaymentMethod()->getId(),
                'items' => $itemsList
            ];
        }

        header('Content-Type: application/json');
        return json_encode($salesList);
    }

    public function showSale($id)
    {
        $sale = $this->entityManager->find(Sale::class, $id);

        if (!$sale) {
            http_response_code(404);
            return "No sale found.";
        }

        $itemsList = array_map(function ($item) {
            return [
                'productId' => $item->getProduct()->getId(),
                'quantity' => $item->getQuantity()
            ];
        }, $sale->getItems()->toArray());

        $result = [
            'id' => $sale->getId(),
            'customer' => $sale->getCustomer()->getId(),
            'paymentMethod' => $sale->getPaymentMethod()->getId(),
            'items' => $itemsList
        ];

        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function updateSale($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $sale = $this->entityManager->find(Sale::class, $id);

        if (!$sale) {
            http_response_code(404);
            return "Sale $id does not exist.";
        }

        if (isset($data['customerId'])) {
            $customer = $this->entityManager->find(Customer::class, $data['customerId']);
            if ($customer) {
                $sale->setCustomer($customer);
            } else {
                http_response_code(404);
                return "Customer not found.";
            }
        }

        if (isset($data['paymentMethodId'])) {
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $data['paymentMethodId']);
            if ($paymentMethod) {
                $sale->setPaymentMethod($paymentMethod);
            } else {
                http_response_code(404);
                return "Payment method not found.";
            }
        }

        foreach ($sale->getItems() as $item) {
            $oldProduct = $item->getProduct();
            $oldProduct->setQuantity($oldProduct->getQuantity() + $item->getQuantity());
            $this->entityManager->remove($item);
        }
        $sale->getItems()->clear();

        $totalPrice = 0;
        foreach ($data['items'] as $itemData) {
            $product = $this->entityManager->find(Product::class, $itemData['productId']);
            if ($product && $product->getQuantity() >= $itemData['quantity']) {
                $saleItem = new SaleItem();
                $saleItem->setProduct($product);
                $saleItem->setQuantity($itemData['quantity']);
                $sale->addItem($saleItem);
                $product->setQuantity($product->getQuantity() - $itemData['quantity']);
                $totalPrice += $product->getPrice() * $itemData['quantity'];
            } else {
                http_response_code(404);
                return "Product {$itemData['productId']} not found or insufficient stock.";
            }
        }

        $this->entityManager->flush();

        return "Sale updated successfully. New Total: $" . $totalPrice;
    }

    public function deleteSale($id)
    {
        $sale = $this->entityManager->find(Sale::class, $id);

        if (!$sale) {
            http_response_code(404);
            return "No sale found.";
        }

        foreach ($sale->getItems() as $item) {
            $product = $item->getProduct();
            $product->setQuantity($product->getQuantity() + $item->getQuantity());
        }

        // Remove cada item individualmente para evitar problemas de foreign key
        foreach ($sale->getItems() as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->remove($sale);
        $this->entityManager->flush();

        return "Deleted sale with ID $id.";
    }
}
