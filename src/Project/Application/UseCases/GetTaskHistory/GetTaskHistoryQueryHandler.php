<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetTaskHistory;

use Src\Project\Infrastructure\Laravel\Models\TaskEventModel;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetTaskHistoryQuery, array<array{type: string, payload: array<string, mixed>, occurred_on: \DateTimeImmutable}>>
 */
final readonly class GetTaskHistoryQueryHandler implements QueryHandlerInterface
{
    /**
     * @return array<array{type: string, payload: array<string, mixed>, occurred_on: \DateTimeImmutable}>
     */
    public function __invoke(QueryInterface $query): array
    {
        /** @var GetTaskHistoryQuery $query */
        return TaskEventModel::query()
            ->where('task_id', $query->taskId)
            ->orderBy('occurred_on')
            ->get()
            ->map(function (TaskEventModel $m) {
                /** @var array<string, mixed> $payload */
                $payload = $m->payload;

                return [
                    'type' => $m->event_type,
                    'payload' => $payload,
                    'occurred_on' => $m->occurred_on->toDateTimeImmutable(),
                ];
            })
            ->all();
    }
}
