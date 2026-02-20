<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Symfony\Entity;

use Doctrine\ORM\Mapping as ORM;
use Src\Identity\Domain\User;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class UserEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', unique: true)]
    private string $external_id;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function fromDomain(User $user): self
    {
        $entity = new self();
        $entity->id = $user->id->value;
        $entity->updateFromDomain($user);

        return $entity;
    }

    public function updateFromDomain(User $user): void
    {
        $this->name = $user->name->value;
        $this->email = $user->email->value;
        $this->external_id = $user->externalId->value;
    }

    public function toDomain(): User
    {
        return new User(
            new UserId($this->id),
            new UserName($this->name),
            new UserEmail($this->email),
            new UserExternalId($this->external_id)
        );
    }
}
