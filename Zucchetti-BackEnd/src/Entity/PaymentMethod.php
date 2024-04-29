<?php
namespace MyProject\Entity;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: "payment_methods")]
class PaymentMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $installments; // Número de parcelas

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("O nome da forma de pagamento não pode estar vazio.");
        }
        $this->name = $name;
    }

    public function getInstallments(): int {
        return $this->installments;
    }

    public function setInstallments(int $installments): void {
        if ($installments < 1) {
            throw new InvalidArgumentException("O número de parcelas deve ser ao menos 1.");
        }
        $this->installments = $installments;
    }
}
