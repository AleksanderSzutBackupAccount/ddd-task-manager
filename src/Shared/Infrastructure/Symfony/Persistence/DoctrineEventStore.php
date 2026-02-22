<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Symfony\Persistence;

use Doctrine\DBAL\Connection;
use Src\Project\Domain\Events\TaskCreated;
use Src\Project\Domain\Events\TaskStatusChanged;
use Src\Shared\Domain\Bus\DomainEventStored;
use Src\Shared\Domain\EventStore\EventStore;

final readonly class DoctrineEventStore implements EventStore
{
    private const TABLE_NAME = 'domain_events';

    public function __construct(private Connection $connection)
    {
    }

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
            $this->connection->insert(self::TABLE_NAME, [
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
        $qb = $this->connection->createQueryBuilder();
        $rows = $qb->select('*')
            ->from(self::TABLE_NAME)
            ->where('aggregate_id = :aggregateId')
            ->orderBy('occurred_on', 'asc')
            ->setParameter('aggregateId', $aggregateId)
            ->executeQuery()
            ->fetchAllAssociative();

        $events = [];
        $map = $this->eventMap();

        foreach ($rows as $row) {
            /** @var string $rowName */
            $rowName = $row['name'];
            $className = $map[$rowName] ?? null;
            if (null === $className) {
                continue;
            }

            /** @var string $rowPayload */
            $rowPayload = $row['payload'];
            /** @var array<string, mixed> $payload */
            $payload = json_decode($rowPayload, true);
            $events[] = $className::fromPrimitives(
                (string) $row['aggregate_id'],
                $payload,
                (string) $row['id'],
                new \DateTimeImmutable((string) $row['occurred_on']),
                (int) $row['version']
            );
        }

        return $events;
    }
}
