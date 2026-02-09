<?php

declare(strict_types=1);


namespace Src\Identity\Domain;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Shared\Domain\Bus\DomainEvent;

final readonly class UserImportedEvent implements DomainEvent
{
    public function __construct(public UserId $id)
    {
    }
}
