<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Identity\Domain\ValueObjects\UserId;

final readonly class Member
{
    public function __construct(
        public UserId $userId,
        public string $name,
    ) {}
}
