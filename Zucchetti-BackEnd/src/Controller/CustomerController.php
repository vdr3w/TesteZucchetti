<?php

namespace MyProject\Controller;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\Customer;

class CustomerController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createCustomer()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name'], $data['cpf'], $data['email'], $data['cep'], $data['address'])) {
            http_response_code(400);
            return "Missing data for name, cpf, email, cep, or address.";
        }

        $customer = new Customer();
        $customer->setName($data['name']);
        $customer->setCpf($data['cpf']);
        $customer->setEmail($data['email']);
        $customer->setCep($data['cep']);
        $customer->setAddress($data['address']);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        try {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Cliente criado com sucesso com ID ' . $customer->getId()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao criar cliente: ' . $e->getMessage()]);
        }
    }

    public function listCustomers()
    {
        try {
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

            header('Content-Type: application/json');
            echo json_encode($customerList);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao listar clientes: ' . $e->getMessage()]);
        }
    }


    public function showCustomer($id)
    {
        try {
            $customer = $this->entityManager->find(Customer::class, $id);

            if (!$customer) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Cliente não encontrado.']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'id' => $customer->getId(),
                'name' => $customer->getName(),
                'cpf' => $customer->getCpf(),
                'email' => $customer->getEmail(),
                'cep' => $customer->getCep(),
                'address' => $customer->getAddress()
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao buscar cliente: ' . $e->getMessage()]);
        }
    }

    public function updateCustomer($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {
            $customer = $this->entityManager->find(Customer::class, $id);
            if (!$customer) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => "Cliente $id não encontrado."]);
                return;
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

            echo json_encode(['success' => true, 'message' => 'Cliente atualizado com sucesso.']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar cliente: ' . $e->getMessage()]);
        }
    }


    public function deleteCustomer($id)
    {
        try {
            $customer = $this->entityManager->find(Customer::class, $id);

            if (!$customer) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Cliente não encontrado.']);
                return;
            }

            $this->entityManager->remove($customer);
            $this->entityManager->flush();

            echo json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso.']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir cliente: ' . $e->getMessage()]);
        }
    }
}
