<?php

declare(strict_types=1);

namespace Src\Identity\Domain;

use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;
use Src\Shared\Domain\Aggregate\AggregateRoot;

final class User extends AggregateRoot
{
    public function __construct(
        public readonly UserId $id,
        public readonly UserName $name,
        public readonly UserEmail $email,
        public readonly UserExternalId $externalId,
    )
    {
    }

    public static function import(
          UserName $name,
          UserEmail $email,
          UserExternalId $externalId): self
    {
        $entity = new self(UserId::generate(), $name, $email, $externalId);

        $entity->record(new UserImportedEvent($entity->id));

        return $entity;
    }
}
