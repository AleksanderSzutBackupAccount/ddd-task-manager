<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Lcobucci;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\UnpermittedClaim;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Src\Identity\Application\Ports\TokenParserInterface;
use Src\Identity\Domain\ValueObjects\UserId;

final readonly class JwtTokenParser implements TokenParserInterface
{
    public function __construct(private LcobucciConfigProvider $configProvider)
    {
    }

    /**
     * @param non-empty-string $token
     * @return UserId|null
     */
    public function parse(string $token): ?UserId
    {
        try {
            $token = $this->configProvider->config->parser()->parse($token);

            $constraints = [
                new SignedWith($this->configProvider->config->signer(), $this->configProvider->config->signingKey()),
                new IssuedBy('coalition-backend'),
                new PermittedFor('coalition-frontend'),
            ];

            if (! $this->configProvider->config->validator()->validate($token, ...$constraints)) {
                return null;
            }

            $uid = $token->claims()->get('uid');

            return new UserId((string) $uid);
        } catch (\Throwable) {
            return null;
        }
    }
}
