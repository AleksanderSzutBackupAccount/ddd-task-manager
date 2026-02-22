<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Entity;

use Doctrine\ORM\Mapping as ORM;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\Task;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskSlug;
use Src\Project\Domain\ValueObjects\TaskStatus;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
class TaskEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 36)]
    private string $project_id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string')]
    private string $status;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $assigned_user_id;

    public static function fromDomain(Task $task): self
    {
        $entity = new self();
        $entity->id = $task->id()->value();
        $entity->slug = $task->slug()->value();
        $entity->project_id = $task->projectId()->value();
        $entity->name = $task->name();
        $entity->description = $task->description();
        $entity->status = $task->status()->value();
        $entity->assigned_user_id = $task->assignedUserId();

        return $entity;
    }

    public function toDomain(): Task
    {
        // W tym projekcie Task jest Event Sourced w Domain, ale Projector zapisuje stan do bazy.
        // Jeśli czytamy z bazy (Read Model), to możemy użyć Task::create jak w TaskModel Laravela.
        $task = Task::create(
            new TaskId($this->id),
            new ProjectId($this->project_id),
            new TaskSlug($this->slug),
            $this->name,
            $this->description,
            new TaskStatus($this->status),
            $this->assigned_user_id ? new UserId($this->assigned_user_id) : null
        );
        $task->pullDomainEvents(); // Usuwamy zdarzenia, bo to tylko odczyt stanu

        return $task;
    }
}
