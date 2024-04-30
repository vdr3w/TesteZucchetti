<?php
namespace MyProject\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "auth_tokens")]
class AuthToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 256)]
    private string $token;

    #[ORM\ManyToOne(targetEntity: "BUser")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private BUser $user;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId(): ?int {
        return $this->id;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function setToken(string $token): void {
        $this->token = $token;
    }

    public function getUser(): BUser {
        return $this->user;
    }

    public function setUser(BUser $user): void {
        $this->user = $user;
    }

    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }
}
