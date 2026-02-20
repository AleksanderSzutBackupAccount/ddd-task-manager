<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\LoginByEmail;

use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<string>
 */
final readonly class LoginByEmailQuery implements QueryInterface
{
    public function __construct(
        public string $email
    ) {}
}
