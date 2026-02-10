<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\Me;

use Src\Identity\Domain\User;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<User>
 */
final readonly class MeQuery implements QueryInterface
{
    public function __construct(public string $userId) {}
}
