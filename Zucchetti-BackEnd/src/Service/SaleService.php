<?php

namespace MyProject\Service;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\Sale;
use MyProject\Entity\Customer;
use MyProject\Entity\Product;
use MyProject\Entity\PaymentMethod;
use MyProject\Entity\SaleItem;
use MyProject\Interface\SaleServiceInterface;

class SaleService implements SaleServiceInterface
{
    private $entityManager;
    private $saleRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->saleRepository = $entityManager->getRepository(Sale::class);
    }

    public function createSale(array $data): array {
        if (!isset($data['customerId'], $data['paymentMethodId'], $data['items'], $data['installments'])) {
            return ['httpCode' => 400, 'body' => json_encode(['success' => false, 'error' => 'Dados faltando para customerId, paymentMethodId, items ou installments.'])];
        }
    
        try {
            $customer = $this->entityManager->find(Customer::class, $data['customerId']);
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $data['paymentMethodId']);
    
            if (!$customer || !$paymentMethod) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Cliente ou método de pagamento não encontrado.'])];
            }
    
            if ($paymentMethod->getInstallments() < $data['installments']) {
                return ['httpCode' => 400, 'body' => json_encode(['success' => false, 'error' => 'Número de parcelas excede o máximo permitido pelo método de pagamento.'])];
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
                    return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => "Produto {$item['productId']} não encontrado ou estoque insuficiente."])];
                }
            }
    
            $this->entityManager->persist($sale);
            $this->entityManager->flush();
    
            $installmentAmount = $totalPrice / $data['installments'];
            
            return ['httpCode' => 201, 'body' => json_encode([
                'success' => true, 
                'message' => "Venda criada com sucesso com ID " . $sale->getId() . ", Total: $" . number_format($totalPrice, 2),
                'installments' => $data['installments'],
                'installmentAmount' => number_format($installmentAmount, 2)
            ])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao criar venda: ' . $e->getMessage()])];
        }
    }

    public function listSales(): array
    {
        try {
            $sales = $this->saleRepository->findAll();
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

            return ['httpCode' => 200, 'body' => json_encode($salesList)];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao listar vendas: ' . $e->getMessage()])];
        }
    }

    public function listSalesByCustomer(int $customerId): array
    {
        try {
            $customer = $this->entityManager->find(Customer::class, $customerId);
            if (!$customer) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Cliente não encontrado.'])];
            }

            $sales = $this->saleRepository->findBy(['customer' => $customer]);
            if (empty($sales)) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Nenhuma venda encontrada para o cliente especificado.'])];
            }

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

            return ['httpCode' => 200, 'body' => json_encode($salesList)];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao listar vendas por cliente: ' . $e->getMessage()])];
        }
    }

    public function showSale(int $id): array
    {
        try {
            $sale = $this->saleRepository->find($id);

            if (!$sale) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Venda não encontrada.'])];
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

            return ['httpCode' => 200, 'body' => json_encode($result)];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao exibir venda: ' . $e->getMessage()])];
        }
    }

    public function updateSale(int $id, array $data): array
    {
        try {
            $sale = $this->saleRepository->find($id);

            if (!$sale) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Venda não existe.'])];
            }

            if (isset($data['customerId'])) {
                $customer = $this->entityManager->find(Customer::class, $data['customerId']);
                if ($customer) {
                    $sale->setCustomer($customer);
                } else {
                    return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Cliente não encontrado.'])];
                }
            }

            if (isset($data['paymentMethodId'])) {
                $paymentMethod = $this->entityManager->find(PaymentMethod::class, $data['paymentMethodId']);
                if ($paymentMethod) {
                    $sale->setPaymentMethod($paymentMethod);
                } else {
                    return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Método de pagamento não encontrado.'])];
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
                    return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => "Produto {$itemData['productId']} não encontrado ou estoque insuficiente."])];
                }
            }

            $this->entityManager->flush();

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => "Venda atualizada com sucesso. Novo Total: $" . $totalPrice])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao atualizar venda: ' . $e->getMessage()])];
        }
    }

    public function deleteSale(int $id): array
    {
        try {
            $sale = $this->saleRepository->find($id);

            if (!$sale) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Venda não encontrada.'])];
            }

            foreach ($sale->getItems() as $item) {
                $product = $item->getProduct();
                $product->setQuantity($product->getQuantity() + $item->getQuantity());
                $this->entityManager->remove($item);
            }

            $this->entityManager->remove($sale);
            $this->entityManager->flush();

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Venda excluída com sucesso com ID ' . $id])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao excluir venda: ' . $e->getMessage()])];
        }
    }
}
