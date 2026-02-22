<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Projectors;

use Doctrine\ORM\EntityManagerInterface;
use Src\Project\Domain\Events\TaskCreated;
use Src\Project\Domain\Events\TaskStatusChanged;
use Src\Project\Infrastructure\Symfony\Entity\TaskEntity;
use Src\Shared\Domain\Bus\DomainEventStored;
use Src\Shared\Domain\Bus\EventProjector;

final readonly class TaskSymfonyProjector implements EventProjector
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function project(DomainEventStored $event): void
    {
        match (true) {
            $event instanceof TaskCreated => $this->projectTaskCreated($event),
            $event instanceof TaskStatusChanged => $this->projectTaskStatusChanged($event),
            default => null,
        };
    }

    private function projectTaskCreated(TaskCreated $event): void
    {
        $ref = new \ReflectionClass(TaskEntity::class);
        $entity = $ref->newInstanceWithoutConstructor();

        $props = [
            'id' => $event->aggregateId(),
            'project_id' => $event->projectId,
            'slug' => $event->slug,
            'name' => $event->name,
            'description' => $event->description,
            'status' => $event->status,
            'assigned_user_id' => $event->assignedUserId,
        ];

        foreach ($props as $name => $value) {
            $p = $ref->getProperty($name);
            $p->setAccessible(true);
            $p->setValue($entity, $value);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function projectTaskStatusChanged(TaskStatusChanged $event): void
    {
        $entity = $this->entityManager->find(TaskEntity::class, $event->aggregateId());
        if ($entity) {
            $ref = new \ReflectionProperty(TaskEntity::class, 'status');
            $ref->setAccessible(true);
            $ref->setValue($entity, $event->newStatus);
            $this->entityManager->flush();
        }
    }
}
