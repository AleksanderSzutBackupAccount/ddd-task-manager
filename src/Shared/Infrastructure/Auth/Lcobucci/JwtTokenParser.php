<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Auth\Lcobucci;

use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validator;
use Src\Shared\Application\Auth\TokenParserInterface;
use Src\Shared\Application\Auth\TokenPayload;
use Src\Shared\Application\Log\LoggerInterface;

final readonly class JwtTokenParser implements TokenParserInterface
{
    public function __construct(
        private LcobucciConfigProvider $configProvider,
        private LoggerInterface $logger
    ) {}

    /**
     * @param  non-empty-string  $token
     */
    public function parse(string $token): ?TokenPayload
    {
        try {
            $parsedToken = $this->getParsedToken($token);

            if (! $this->getValidator()->validate($parsedToken, ...$this->getConstraints())) {
                return null;
            }

            return new TokenPayload(
                (string)$parsedToken->claims()->get('sub'),
                $parsedToken->claims()->all()
            );
        } catch (\Throwable $e) {
            $this->logger->error('JWT Token parsing failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * @param  non-empty-string  $plainToken
     */
    private function getParsedToken(string $plainToken): UnencryptedToken
    {
        $token = $this->configProvider->config->parser()->parse($plainToken);

        assert($token instanceof UnencryptedToken);

        return $token;
    }

    private function getValidator(): Validator
    {
        return $this->configProvider->config->validator();
    }

    /**
     * @return Constraint[]
     */
    private function getConstraints(): array
    {
        return [
            new SignedWith($this->configProvider->config->signer(), $this->configProvider->config->signingKey()),
            new IssuedBy(LcobucciConfigProvider::ISSUER),
            new PermittedFor(LcobucciConfigProvider::PERMITTED_FOR),
        ];
    }

}
