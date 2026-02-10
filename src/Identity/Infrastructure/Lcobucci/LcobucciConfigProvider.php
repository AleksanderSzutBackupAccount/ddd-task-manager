<?php

declare(strict_types=1);


namespace Src\Identity\Infrastructure\Lcobucci;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

final readonly class LcobucciConfigProvider
{
    public Configuration $config;

    /**
     * @param  non-empty-string  $secret
     */
    public function __construct(string $secret)
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256,
            InMemory::plainText($secret)
        );
    }

    /**
     * @param non-empty-string $decodedSecret
     * @return static
     */
    public static function fromDecoded(string $decodedSecret): static
    {
        if (!str_starts_with($decodedSecret, 'base64:')) {
            throw new \DomainException('secret is not decoded');
        }
        /** @var non-empty-string $secret */
        $secret = base64_decode(substr($decodedSecret, 7));

        return new self($secret);
    }

}
