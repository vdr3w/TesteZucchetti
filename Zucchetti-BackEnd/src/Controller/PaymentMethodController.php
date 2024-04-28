<?php

namespace MyProject\Controller;

use Doctrine\ORM\EntityManager;
use MyProject\Entity\PaymentMethod;

class PaymentMethodController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createPaymentMethod()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name'], $data['installments'])) {
            http_response_code(400);
            return "Missing data for name or installments.";
        }

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setName($data['name']);
        $paymentMethod->setInstallments((int) $data['installments']);

        $this->entityManager->persist($paymentMethod);
        $this->entityManager->flush();

        http_response_code(201);
        return "Created Payment Method with ID " . $paymentMethod->getId() . "\n";
    }

    public function listPaymentMethods()
    {
        $paymentMethods = $this->entityManager->getRepository(PaymentMethod::class)->findAll();
        $paymentMethodList = [];

        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodList[] = [
                'id' => $paymentMethod->getId(),
                'name' => $paymentMethod->getName(),
                'installments' => $paymentMethod->getInstallments()
            ];
        }

        header('Content-Type: application/json');
        return json_encode($paymentMethodList);
    }
}
