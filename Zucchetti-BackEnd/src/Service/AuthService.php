<?php
// src/Service/AuthService.php
namespace MyProject\Service; // Adiciona o namespace correto

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\Clock\SystemClock;
use MyProject\Entity\BUser;
use DateTimeZone;
use MyProject\Entity\AuthToken;

class AuthService
{
    private $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('chave_secreta') // Chave secreta diretamente como texto
        );
    }

    public function generateToken(BUser $user)
    {
        $now = new \DateTimeImmutable();
        $token = $this->config->builder()
            ->issuedBy('http://localhost:8000') // Configura o emissor (URL do backend)
            ->permittedFor('http://localhost:5173') // Configura o público (URL do frontend)
            ->issuedAt($now) // Configura o tempo de emissão
            ->expiresAt($now->modify('+1 hour')) // Configura a expiração de 1 hora
            ->withClaim('uid', $user->getId()) // Configura um claim personalizado com o ID do usuário
            ->getToken($this->config->signer(), $this->config->signingKey()); // Gera o token

        return $token->toString();
    }

    public function validateToken($token)
    {
        $token = $this->config->parser()->parse($token);
        $constraints = [
            new IssuedBy('http://localhost:8000'),
            new SignedWith(new Sha256(), InMemory::plainText('chave_secreta')),
        ];

        return $this->config->validator()->validate($token, ...$constraints);
    }
    // Adicionar no AuthService

public function saveToken(AuthToken $token)
{
    global $entityManager;
    $entityManager->persist($token);
    $entityManager->flush();
}

public function deleteTokensForUser(BUser $user)
{
    global $entityManager;
    $query = $entityManager->createQuery('DELETE FROM MyProject\Entity\AuthToken a WHERE a.user = :user');
    $query->setParameter('user', $user);
    $query->execute();
}

}
