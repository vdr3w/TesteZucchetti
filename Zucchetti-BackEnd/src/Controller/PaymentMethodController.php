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
            echo json_encode(['success' => false, 'error' => 'Dados faltando para nome ou parcelas.']);
            return;
        }

        try {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->setName($data['name']);
            $paymentMethod->setInstallments((int) $data['installments']);

            $this->entityManager->persist($paymentMethod);
            $this->entityManager->flush();

            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Método de pagamento criado com sucesso com ID ' . $paymentMethod->getId()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao criar método de pagamento: ' . $e->getMessage()]);
        }
    }

    public function listPaymentMethods()
    {
        try {
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
            echo json_encode($paymentMethodList);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao listar métodos de pagamento: ' . $e->getMessage()]);
        }
    }

    public function showPaymentMethod($id)
    {
        try {
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $id);

            if (!$paymentMethod) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Método de pagamento não encontrado.']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'id' => $paymentMethod->getId(),
                'name' => $paymentMethod->getName(),
                'installments' => $paymentMethod->getInstallments()
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao exibir método de pagamento: ' . $e->getMessage()]);
        }
    }

    public function updatePaymentMethod($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $id);
            if (!$paymentMethod) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => "Método de pagamento $id não existe."]);
                return;
            }

            if (isset($data['name'])) {
                $paymentMethod->setName($data['name']);
            }
            if (isset($data['installments'])) {
                $paymentMethod->setInstallments((int) $data['installments']);
            }

            $this->entityManager->flush();

            echo json_encode(['success' => true, 'message' => 'Método de pagamento atualizado com sucesso.']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar método de pagamento: ' . $e->getMessage()]);
        }
    }

    public function deletePaymentMethod($id)
    {
        try {
            $paymentMethod = $this->entityManager->find(PaymentMethod::class, $id);

            if (!$paymentMethod) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Método de pagamento não encontrado.']);
                return;
            }

            $this->entityManager->remove($paymentMethod);
            $this->entityManager->flush();

            echo json_encode(['success' => true, 'message' => 'Método de pagamento excluído com sucesso.']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir método de pagamento: ' . $e->getMessage()]);
        }
    }
}
