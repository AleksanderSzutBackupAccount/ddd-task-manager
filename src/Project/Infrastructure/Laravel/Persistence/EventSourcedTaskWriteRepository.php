<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Persistence;

use Illuminate\Support\Facades\DB;
use Src\Project\Domain\Task;
use Src\Project\Domain\TaskWriteRepository;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Shared\Domain\Bus\DomainEventStored;
use Src\Shared\Domain\Bus\EventProjector;
use Src\Shared\Domain\EventStore\EventStore;

final readonly class EventSourcedTaskWriteRepository implements TaskWriteRepository
{
    public function __construct(
        private EventStore $eventStore,
        private EventProjector $projector
    ) {}

    public function save(Task $task): void
    {
        $events = $task->pullDomainEvents();

        DB::transaction(function () use ($events): void {
            /** @var DomainEventStored[] $events */
            $this->eventStore->append($events);

            foreach ($events as $event) {
                $this->projector->project($event);
            }
        });
    }

    public function findById(TaskId $id): ?Task
    {
        $events = $this->eventStore->getEventsFor((string) $id);

        if (empty($events)) {
            return null;
        }

        return Task::reconstitute($events);
    }
}
