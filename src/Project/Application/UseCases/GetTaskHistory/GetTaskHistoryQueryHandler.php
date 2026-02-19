<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetTaskHistory;

use Src\Project\Domain\TaskReadRepository;
use Src\Project\Domain\ValueObjects\TaskId;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;
use Src\Shared\Domain\Bus\DomainEventStored;

/**
 * @implements QueryHandlerInterface<GetTaskHistoryQuery, array<array{type: string, payload: array<string, mixed>, occurred_on: \DateTimeImmutable, version: int}>>
 */
final readonly class GetTaskHistoryQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TaskReadRepository $tasks) {}

    /**
     * @return array<array{type: string, payload: array<string, mixed>, occurred_on: \DateTimeImmutable, version: int}>
     */
    public function __invoke(QueryInterface $query): array
    {
        /** @var GetTaskHistoryQuery $query */
        $events = $this->tasks->getHistory(new TaskId($query->taskId));

        return array_map(static function (DomainEventStored $event): array {
            return [
                'type' => $event::eventName(),
                'payload' => $event->toPrimitives(),
                'occurred_on' => $event->occurredOn(),
                'version' => $event->version(),
            ];
        }, $events);
    }
}
