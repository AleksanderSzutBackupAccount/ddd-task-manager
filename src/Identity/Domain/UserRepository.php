<?php

declare(strict_types=1);

namespace Src\Identity\Domain;

use Src\Identity\Domain\ValueObjects\UserId;

interface UserRepository
{
    public function upsert(User $user): void;

    public function find(UserId $id): ?User;

    public function all(): UserCollection;
}
