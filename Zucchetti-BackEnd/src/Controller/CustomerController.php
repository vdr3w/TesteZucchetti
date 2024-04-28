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

        http_response_code(201);
        return "Created Customer with ID " . $customer->getId() . "\n";
    }
}
