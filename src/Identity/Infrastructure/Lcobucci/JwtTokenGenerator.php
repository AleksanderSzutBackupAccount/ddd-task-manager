<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Lcobucci;

use DateTimeImmutable;
use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Domain\User;

final readonly class JwtTokenGenerator implements TokenGeneratorInterface
{
    public function __construct(private LcobucciConfigProvider $configProvider) {}

    public function generate(User $user): string
    {
        $now = new DateTimeImmutable;

        return $this->configProvider->config->builder()
            ->issuedBy(LcobucciConfigProvider::ISSUER)
            ->permittedFor(LcobucciConfigProvider::PERMITTED_FOR)
            ->identifiedBy($user->id->value)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify(LcobucciConfigProvider::DEFAULT_TTL))
            ->withClaim(LcobucciConfigProvider::CLAIM_UID, $user->id->value)
            ->withClaim(LcobucciConfigProvider::CLAIM_EMAIL, $user->email->value)
            ->getToken($this->configProvider->config->signer(), $this->configProvider->config->signingKey())
            ->toString();
    }
}
