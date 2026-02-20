<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Infrastructure\Symfony\Entity\UserEntity;
use Src\Project\Domain\Collections\ProjectCollection;
use Src\Project\Domain\Project;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Project\Infrastructure\Symfony\Entity\ProjectEntity;

final readonly class ProjectSymfonyRepository implements ProjectRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Project $project): void
    {
        /** @var ProjectEntity $entity */
        $entity = $this->entityManager->find(ProjectEntity::class, $project->id()->value())
            ?? ProjectEntity::fromDomain($project);

        if ($this->entityManager->contains($entity)) {
            // Update fields manually or use reflection/mapper
            $refName = new \ReflectionProperty($entity, 'name');
            $refName->setAccessible(true);
            $refName->setValue($entity, $project->name());

            $refSlug = new \ReflectionProperty($entity, 'slug');
            $refSlug->setAccessible(true);
            $refSlug->setValue($entity, $project->slug()->value());
        } else {
            $this->entityManager->persist($entity);
        }

        // Sync users
        $usersRef = new \ReflectionProperty($entity, 'users');
        $usersRef->setAccessible(true);
        /** @var \Doctrine\Common\Collections\Collection<int, UserEntity> $usersCollection */
        $usersCollection = $usersRef->getValue($entity);
        $usersCollection->clear();

        foreach ($project->userIds() as $userId) {
            $userEntity = $this->entityManager->find(UserEntity::class, $userId);
            if ($userEntity instanceof UserEntity) {
                $usersCollection->add($userEntity);
            }
        }

        $this->entityManager->flush();
    }

    public function findById(ProjectId $id): ?Project
    {
        /** @var ProjectEntity|null $entity */
        $entity = $this->entityManager->find(ProjectEntity::class, $id->value());

        return $entity?->toDomain();
    }

    public function findBySlug(ProjectSlug $slug): ?Project
    {
        /** @var ProjectEntity|null $entity */
        $entity = $this->entityManager->getRepository(ProjectEntity::class)->findOneBy(['slug' => $slug->value()]);

        return $entity?->toDomain();
    }

    public function getAssignedToUser(UserId $userId): ProjectCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
           ->from(ProjectEntity::class, 'p')
           ->join('p.users', 'u')
           ->where('u.id = :userId')
           ->setParameter('userId', $userId->value);

        /** @var ProjectEntity[] $entities */
        $entities = $qb->getQuery()->getResult();

        return new ProjectCollection(array_map(fn (ProjectEntity $e) => $e->toDomain(), $entities));
    }
}
