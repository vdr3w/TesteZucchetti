<?php

namespace MyProject\Service;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\PaymentMethod;
use MyProject\Interface\PaymentMethodServiceInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class PaymentMethodService implements PaymentMethodServiceInterface
{
    private $entityManager;
    private $cache;

    public function __construct(EntityManager $entityManager, FilesystemAdapter $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function createPaymentMethod(array $data): array
    {
        if (!isset($data['name'], $data['installments'])) {
            return ['httpCode' => 400, 'body' => json_encode(['success' => false, 'error' => 'Dados faltando para nome ou parcelas.'])];
        }

        try {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->setName($data['name']);
            $paymentMethod->setInstallments((int) $data['installments']);

            $this->entityManager->persist($paymentMethod);
            $this->entityManager->flush();

            $this->cache->delete('payment_method_list_cache');

            return ['httpCode' => 201, 'body' => json_encode(['success' => true, 'message' => 'Método de pagamento criado com sucesso com ID ' . $paymentMethod->getId()])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao criar método de pagamento: ' . $e->getMessage()])];
        }
    }

    public function listPaymentMethods(): array
    {
        $cacheKey = 'payment_method_list_cache';

        $cachedData = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $paymentMethods = $this->entityManager->getRepository(PaymentMethod::class)->findAll();
            $paymentMethodList = [];
            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethodList[] = [
                    'id' => $paymentMethod->getId(),
                    'name' => $paymentMethod->getName(),
                    'installments' => $paymentMethod->getInstallments()
                ];
            }
            return $paymentMethodList;
        });

        if ($cachedData === false) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao recuperar dados do cache.'])];
        }

        if (!is_array($cachedData)) {
            $cachedData = json_decode($cachedData, true);
        }

        return ['httpCode' => 200, 'body' => json_encode($cachedData)];
    }


    public function showPaymentMethod(int $id): array
    {
        $cacheKey = "payment_method_$id";

        $cachedData = $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $id);
            if (!$paymentMethod) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Método de pagamento não encontrado.'])];
            }
            return [
                'id' => $paymentMethod->getId(),
                'name' => $paymentMethod->getName(),
                'installments' => $paymentMethod->getInstallments()
            ];
        });

        return ['httpCode' => 200, 'body' => json_encode($cachedData)];
    }

    public function updatePaymentMethod(int $id, array $data): array
    {
        try {
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $id);
            if (!$paymentMethod) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => "Método de pagamento $id não existe."])];
            }

            if (isset($data['name'])) {
                $paymentMethod->setName($data['name']);
            }
            if (isset($data['installments'])) {
                $paymentMethod->setInstallments((int) $data['installments']);
            }

            $this->entityManager->flush();
            $this->cache->delete('payment_method_list_cache');


            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Método de pagamento atualizado com sucesso.'])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao atualizar método de pagamento: ' . $e->getMessage()])];
        }
    }

    public function deletePaymentMethod(int $id): array
    {
        try {
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $id);

            if (!$paymentMethod) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Método de pagamento não encontrado.'])];
            }

            $this->entityManager->remove($paymentMethod);
            $this->entityManager->flush();
            $this->cache->delete('payment_method_list_cache');

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Método de pagamento excluído com sucesso.'])];
        } catch (\Exception $e) {
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao excluir método de pagamento: ' . $e->getMessage()])];
        }
    }
}
