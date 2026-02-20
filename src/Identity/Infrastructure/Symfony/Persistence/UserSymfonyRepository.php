<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Symfony\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserCollection;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Infrastructure\Symfony\Entity\UserEntity;

final readonly class UserSymfonyRepository implements UserRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function upsert(User $user): void
    {
        /** @var UserEntity|null $entity */
        $entity = $this->entityManager->find(UserEntity::class, $user->id->value);

        if ($entity) {
            $entity->updateFromDomain($user);
        } else {
            $entity = UserEntity::fromDomain($user);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    public function find(UserId $id): ?User
    {
        /** @var UserEntity|null $entity */
        $entity = $this->entityManager->find(UserEntity::class, $id->value);

        return $entity?->toDomain();
    }

    public function findByEmail(UserEmail $email): ?User
    {
        /** @var UserEntity|null $entity */
        $entity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['email' => $email->value]);

        return $entity?->toDomain();
    }

    public function all(): UserCollection
    {
        /** @var UserEntity[] $entities */
        $entities = $this->entityManager->getRepository(UserEntity::class)->findBy([], ['id' => 'ASC']);

        $users = array_map(fn (UserEntity $entity) => $entity->toDomain(), $entities);

        return UserCollection::new(...$users);
    }
}
