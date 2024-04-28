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
            echo json_encode(['success' => false, 'error' => 'Dados faltando para customerId, paymentMethodId ou items.']);
            return;
        }

        try {
            $customer = $this->entityManager->find(Customer::class, $data['customerId']);
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $data['paymentMethodId']);

            if (!$customer || !$paymentMethod) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Cliente ou método de pagamento não encontrado.']);
                return;
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
                    echo json_encode(['success' => false, 'error' => "Produto {$item['productId']} não encontrado ou estoque insuficiente."]);
                    return;
                }
            }

            $this->entityManager->persist($sale);
            $this->entityManager->flush();

            http_response_code(201);
            echo json_encode(['success' => true, 'message' => "Venda criada com sucesso com ID " . $sale->getId() . " Total: $" . $totalPrice]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao criar venda: ' . $e->getMessage()]);
        }
    }

    public function listSales()
    {
        try {
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
            echo json_encode($salesList);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao listar vendas: ' . $e->getMessage()]);
        }
    }

    public function showSale($id)
    {
        try {
            $sale = $this->entityManager->find(Sale::class, $id);

            if (!$sale) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Venda não encontrada.']);
                return;
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
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao exibir venda: ' . $e->getMessage()]);
        }
    }

    public function updateSale($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {
            $sale = $this->entityManager->find(Sale::class, $id);

            if (!$sale) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Venda não existe.']);
                return;
            }

            if (isset($data['customerId'])) {
                $customer = $this->entityManager->find(Customer::class, $data['customerId']);
                if ($customer) {
                    $sale->setCustomer($customer);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Cliente não encontrado.']);
                    return;
                }
            }

            if (isset($data['paymentMethodId'])) {
                $paymentMethod = $this->entityManager->find(PaymentMethod::class, $data['paymentMethodId']);
                if ($paymentMethod) {
                    $sale->setPaymentMethod($paymentMethod);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Método de pagamento não encontrado.']);
                    return;
                }
            }

            foreach ($sale->getItems() as $oldItem) {
                $oldProduct = $oldItem->getProduct();
                $oldProduct->setQuantity($oldProduct->getQuantity() + $oldItem->getQuantity());
                $this->entityManager->remove($oldItem);
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
                    echo json_encode(['success' => false, 'error' => "Produto {$itemData['productId']} não encontrado ou estoque insuficiente."]);
                    return;
                }
            }

            $this->entityManager->flush();

            echo json_encode(['success' => true, 'message' => "Venda atualizada com sucesso. Novo Total: $" . $totalPrice]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar venda: ' . $e->getMessage()]);
        }
    }

    public function deleteSale($id)
    {
        try {
            $sale = $this->entityManager->find(Sale::class, $id);

            if (!$sale) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Venda não encontrada.']);
                return;
            }

            foreach ($sale->getItems() as $item) {
                $product = $item->getProduct();
                $product->setQuantity($product->getQuantity() + $item->getQuantity());
                $this->entityManager->remove($item);
            }

            $this->entityManager->remove($sale);
            $this->entityManager->flush();

            echo json_encode(['success' => true, 'message' => 'Venda excluída com sucesso com ID ' . $id]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir venda: ' . $e->getMessage()]);
        }
    }
}
