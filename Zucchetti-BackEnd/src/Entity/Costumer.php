<?php
namespace MyProject\Entity;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: "customers")]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 11)]
    private string $cpf;

    #[ORM\Column(type: 'string', length: 100)]
    private string $email;

    #[ORM\Column(type: 'string', length: 8)]
    private string $cep;

    #[ORM\Column(type: 'string', length: 255)]
    private string $address;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        if (empty($name)) {
            throw new InvalidArgumentException("O nome não pode estar vazio.");
        }
        $this->name = $name;
    }

    public function getCpf(): string {
        return $this->cpf;
    }

    public function setCpf(string $cpf): void {
        if (!preg_match("/^\d{3}\.\d{3}\.\d{3}-\d{2}$/", $cpf)) {
            throw new InvalidArgumentException("CPF inválido. Formato esperado: 000.000.000-00.");
        }
        $this->cpf = $cpf;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Endereço de e-mail inválido.");
        }
        $this->email = $email;
    }

    public function getCep(): string {
        return $this->cep;
    }

    public function setCep(string $cep): void {
        if (!preg_match("/^\d{5}-\d{3}$/", $cep)) {
            throw new InvalidArgumentException("CEP inválido. Formato esperado: 00000-000.");
        }
        $this->cep = $cep;
    }

    public function getAddress(): string {
        return $this->address;
    }

    public function setAddress(string $address): void {
        if (empty($address)) {
            throw new InvalidArgumentException("O endereço não pode estar vazio.");
        }
        $this->address = $address;
    }
}
