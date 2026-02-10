<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Lcobucci;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Domain\User;

final readonly class JwtTokenGenerator implements TokenGeneratorInterface
{

    public function __construct(private LcobucciConfigProvider $configProvider)
    {
    }


    public function generate(User $user): string
    {
        $now = new DateTimeImmutable;

        return $this->configProvider->config->builder()
            ->issuedBy('coalition-backend')
            ->permittedFor('coalition-frontend')
            ->identifiedBy($user->id->value)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $user->id->value)
            ->withClaim('email', $user->email->value)
            ->getToken($this->configProvider->config->signer(), $this->configProvider->config->signingKey())
            ->toString();
    }
}
