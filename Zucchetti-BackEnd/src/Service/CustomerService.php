<?php

namespace MyProject\Service;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\Customer;
use MyProject\Interface\CustomerServiceInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CustomerService implements CustomerServiceInterface {
    private $entityManager;
    private $cache;

    public function __construct(EntityManager $entityManager, FilesystemAdapter $cache) {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function createCustomer(array $data): array {
        if (!isset($data['name'], $data['cpf'], $data['email'], $data['cep'], $data['address'])) {
            http_response_code(400);
            return ['httpCode' => 400, 'body' => json_encode(['success' => false, 'message' => "Missing data for name, cpf, email, cep, or address."])];
        }

        $customer = new Customer();
        $customer->setName($data['name']);
        $customer->setCpf($data['cpf']);
        $customer->setEmail($data['email']);
        $customer->setCep($data['cep']);
        $customer->setAddress($data['address']);

        try {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
            $this->cache->delete('customer_list_cache');

            return ['httpCode' => 201, 'body' => json_encode(['success' => true, 'message' => 'Cliente criado com sucesso com ID ' . $customer->getId()])];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao criar cliente: ' . $e->getMessage()])];
        }
    }

    public function listCustomers(): array {
        $cacheKey = 'customer_list_cache';

        $cachedData = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $customers = $this->entityManager->getRepository(Customer::class)->findAll();
            $customerList = [];
            foreach ($customers as $customer) {
                $customerList[] = [
                    'id' => $customer->getId(),
                    'name' => $customer->getName(),
                    'cpf' => $customer->getCpf(),
                    'email' => $customer->getEmail(),
                    'cep' => $customer->getCep(),
                    'address' => $customer->getAddress()
                ];
            }
            return $customerList;
        });

        return ['httpCode' => 200, 'body' => json_encode($cachedData)];
    }

    public function showCustomer(int $id): array {
        $cacheKey = "customer_$id";

        $cachedData = $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            $customer = $this->entityManager->find(Customer::class, $id);
            if (!$customer) {
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Cliente não encontrado.'])];
            }
            return [
                'id' => $customer->getId(),
                'name' => $customer->getName(),
                'cpf' => $customer->getCpf(),
                'email' => $customer->getEmail(),
                'cep' => $customer->getCep(),
                'address' => $customer->getAddress()
            ];
        });

        return ['httpCode' => 200, 'body' => json_encode($cachedData)];
    }

    public function updateCustomer(int $id, array $data): array {
        try {
            $customer = $this->entityManager->find(Customer::class, $id);
            if (!$customer) {
                http_response_code(404);
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => "Cliente $id não encontrado."])];
            }

            if (isset($data['name'])) {
                $customer->setName($data['name']);
            }
            if (isset($data['cpf'])) {
                $customer->setCpf($data['cpf']);
            }
            if (isset($data['email'])) {
                $customer->setEmail($data['email']);
            }
            if (isset($data['cep'])) {
                $customer->setCep($data['cep']);
            }
            if (isset($data['address'])) {
                $customer->setAddress($data['address']);
            }

            $this->entityManager->flush();
            $this->cache->delete('customer_list_cache');

            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Cliente atualizado com sucesso.'])];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao atualizar cliente: ' . $e->getMessage()])];
        }
    }

    public function deleteCustomer(int $id): array {
        try {
            $customer = $this->entityManager->find(Customer::class, $id);

            if (!$customer) {
                http_response_code(404);
                return ['httpCode' => 404, 'body' => json_encode(['success' => false, 'error' => 'Cliente não encontrado.'])];
            }

            $this->entityManager->remove($customer);
            $this->entityManager->flush();
            $this->cache->delete('customer_list_cache');


            return ['httpCode' => 200, 'body' => json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso.'])];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['httpCode' => 500, 'body' => json_encode(['success' => false, 'error' => 'Erro ao excluir cliente: ' . $e->getMessage()])];
        }
    }
}
