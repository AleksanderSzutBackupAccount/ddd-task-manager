<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\GetUsers;

use Src\Identity\Domain\User;
use Src\Identity\Domain\UserCollection;

final readonly class UserResponse
{
    public function __construct(private UserCollection $users) {}

    /**
     * @return array<mixed>
     */
    public function toResponse(): array
    {
        return $this->users->map(fn (User $user) => ['id' => $user->id->value, 'name' => $user->name->value]);
    }
}
