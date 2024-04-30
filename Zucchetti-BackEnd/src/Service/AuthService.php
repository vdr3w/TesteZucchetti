<?php

namespace MyProject\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use MyProject\Entity\BUser;
use MyProject\Entity\AuthToken;

class AuthService
{
    private $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('chave_secreta')
        );
    }

    public function generateToken(BUser $user)
    {
        $now = new \DateTimeImmutable();
        $token = $this->config->builder()
            ->issuedBy('http://localhost:8000')
            ->permittedFor('http://localhost:5173')
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $user->getId())
            ->getToken($this->config->signer(), $this->config->signingKey());

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
