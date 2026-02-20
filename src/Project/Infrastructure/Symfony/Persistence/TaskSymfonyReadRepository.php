<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\Collections\TaskCollection;
use Src\Project\Domain\TaskReadRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Infrastructure\Symfony\Entity\ProjectEntity;
use Src\Project\Infrastructure\Symfony\Entity\TaskEntity;
use Src\Shared\Domain\EventStore\EventStore;

final readonly class TaskSymfonyReadRepository implements TaskReadRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventStore $eventStore,
    ) {
    }

    public function findAll(ProjectId $projectId): TaskCollection
    {
        /** @var TaskEntity[] $entities */
        $entities = $this->entityManager->getRepository(TaskEntity::class)->findBy(['project_id' => $projectId->value]);

        return new TaskCollection(array_map(fn (TaskEntity $e) => $e->toDomain(), $entities));
    }

    public function findByUserId(UserId $userId): TaskCollection
    {
        // Tasks from projects where user is a member AND assigned to the user
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
           ->from(TaskEntity::class, 't')
           ->innerJoin(ProjectEntity::class, 'p', 'WITH', 't.project_id = p.id')
           ->innerJoin('p.users', 'u')
           ->where('u.id = :userId')
           ->andWhere('t.assigned_user_id = :userId')
           ->setParameter('userId', $userId->value);

        /** @var TaskEntity[] $entities */
        $entities = $qb->getQuery()->getResult();

        return new TaskCollection(array_map(fn (TaskEntity $e) => $e->toDomain(), $entities));
    }

    public function getHistory(TaskId $id): array
    {
        $events = $this->eventStore->getEventsFor((string) $id);
        usort($events, static fn (\Src\Shared\Domain\Bus\DomainEventStored $a, \Src\Shared\Domain\Bus\DomainEventStored $b) => $a->version() <=> $b->version());

        return $events;
    }

    public function countByProject(ProjectId $projectId): int
    {
        return $this->entityManager->getRepository(TaskEntity::class)->count(['project_id' => $projectId->value]);
    }
}
