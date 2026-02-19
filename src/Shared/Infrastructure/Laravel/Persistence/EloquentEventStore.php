<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Laravel\Persistence;

use Illuminate\Support\Facades\DB;
use Src\Project\Domain\Events\TaskCreated;
use Src\Project\Domain\Events\TaskStatusChanged;
use Src\Shared\Domain\Bus\DomainEventStored;
use Src\Shared\Domain\EventStore\EventStore;

final class EloquentEventStore implements EventStore
{
    private const TABLE_NAME = 'domain_event';

    /**
     * @return array<string, class-string<DomainEventStored>>
     */
    private function eventMap(): array
    {
        return [
            TaskCreated::eventName() => TaskCreated::class,
            TaskStatusChanged::eventName() => TaskStatusChanged::class,
        ];
    }

    public function append(array $events): void
    {
        foreach ($events as $event) {
            DB::table(self::TABLE_NAME)->insert([
                'id' => $event->eventId(),
                'aggregate_id' => $event->aggregateId(),
                'name' => $event->eventName(),
                'payload' => json_encode($event->toPrimitives()),
                'occurred_on' => $event->occurredOn()->format('Y-m-d H:i:s'),
                'version' => $event->version(),
            ]);
        }
    }

    public function getEventsFor(string $aggregateId): array
    {
        $rows = DB::table(self::TABLE_NAME)
            ->where('aggregate_id', $aggregateId)
            ->orderBy('occurred_on', 'asc')
            ->get();

        $events = [];
        $map = $this->eventMap();

        foreach ($rows as $row) {
            /** @var object{aggregate_id: string, payload: string, occured_on: string, name: string, id: string, occurred_on: string, version: int} $row */
            $className = $map[$row->name] ?? null;
            if ($className === null) {
                continue;
            }

            /** @var array<string, mixed> $payload */
            $payload = json_decode($row->payload, true);
            $events[] = $className::fromPrimitives(
                $row->aggregate_id,
                $payload,
                $row->id,
                new \DateTimeImmutable($row->occurred_on),
                (int) $row->version
            );
        }

        return $events;
    }
}
