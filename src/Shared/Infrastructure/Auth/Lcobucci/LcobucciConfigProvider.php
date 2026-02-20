<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Auth\Lcobucci;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final readonly class LcobucciConfigProvider
{
    public const ISSUER = 'task-manager-backend';

    public const PERMITTED_FOR = 'task-manager-frontend';

    public const DEFAULT_TTL = '+1 hour';

    public const CLAIM_UID = 'uid';

    public const CLAIM_EMAIL = 'email';

    public Configuration $config;

    public function __construct(string $secret)
    {
        if (str_starts_with($secret, 'base64:')) {
            $secret = (string) base64_decode(substr($secret, 7));
        }

        if ('' === $secret) {
            throw new InvalidConfigurationException('App key cannot be empty');
        }

        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($secret)
        );
    }
}
