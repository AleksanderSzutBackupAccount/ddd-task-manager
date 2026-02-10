<?php

declare(strict_types=1);

namespace Src\Identity\Domain;

use Src\Shared\Domain\Collection\Collection;

/**
 * @extends Collection<User>
 */
final class UserCollection extends Collection
{
    protected function type(): string
    {
        return User::class;
    }
}
