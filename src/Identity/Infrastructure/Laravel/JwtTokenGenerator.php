<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Laravel;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Domain\User;

final readonly class JwtTokenGenerator implements TokenGeneratorInterface
{
    private Configuration $config;

    /**
     * @param non-empty-string $secret
     */
    public function __construct(string $secret)
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256,
            InMemory::plainText($secret)
        );
    }

    public function generate(User $user): string
    {
        $now = new DateTimeImmutable;

        return $this->config->builder()
            ->issuedBy('coalition-backend')
            ->permittedFor('coalition-frontend')
            ->identifiedBy($user->id->value)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', (string) $user->id)
            ->withClaim('email', (string) $user->email)
            ->getToken($this->config->signer(), $this->config->signingKey())
            ->toString();
    }
}
