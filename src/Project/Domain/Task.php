<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Project\Domain\Events\TaskCreated;
use Src\Project\Domain\Events\TaskStatusChanged;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Project\Domain\ValueObjects\TaskStatus;
use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Domain\Bus\DomainEvent;

final class Task extends AggregateRoot
{
    private TaskId $id;
    private ProjectId $projectId;
    private string $name;
    private string $description;
    private TaskStatus $status;
    private ?string $assignedUserId;

    public function __construct() {}

    public static function create(
        TaskId $id,
        ProjectId $projectId,
        string $name,
        string $description,
        TaskStatus $status,
        ?string $assignedUserId
    ): self {
        $task = new self();
        $task->recordAndApply(new TaskCreated(
            $id->value(),
            $projectId->value(),
            $name,
            $description,
            $status->value(),
            $assignedUserId
        ));

        return $task;
    }

    public function changeStatus(TaskStatus $newStatus): void
    {
        if (!$this->status->equals($newStatus)) {
            $this->recordAndApply(new TaskStatusChanged(
                $this->id->value(),
                $this->status->value(),
                $newStatus->value()
            ));
        }
    }

    private function recordAndApply(DomainEvent $event): void
    {
        $this->record($event);
        $this->apply($event);
    }

    public function apply(DomainEvent $event): void
    {
        if ($event instanceof TaskCreated) {
            $this->id = new TaskId($event->taskId);
            $this->projectId = new ProjectId($event->projectId);
            $this->name = $event->name;
            $this->description = $event->description;
            $this->status = new TaskStatus($event->status);
            $this->assignedUserId = $event->assignedUserId;
        } elseif ($event instanceof TaskStatusChanged) {
            $this->status = new TaskStatus($event->newStatus);
        }
    }

    public static function reconstitute(array $events): self
    {
        $task = new self();
        foreach ($events as $event) {
            $task->apply($event);
        }
        return $task;
    }

    public function id(): TaskId
    {
        return $this->id;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function status(): TaskStatus
    {
        return $this->status;
    }

    public function assignedUserId(): ?string
    {
        return $this->assignedUserId;
    }
}
