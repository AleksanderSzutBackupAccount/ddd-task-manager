<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetTaskHistory;

use Src\Project\Infrastructure\Laravel\Models\TaskEventModel;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetTaskHistoryQuery, array>
 */
final readonly class GetTaskHistoryQueryHandler implements QueryHandlerInterface
{
    public function __invoke(QueryInterface $query): array
    {
        /** @var GetTaskHistoryQuery $query */
        return TaskEventModel::query()
            ->where('task_id', $query->taskId)
            ->orderBy('occurred_on')
            ->get()
            ->map(fn (TaskEventModel $m) => [
                'type' => $m->event_type,
                'payload' => $m->payload,
                'occurred_on' => $m->occurred_on,
            ])
            ->all();
    }
}
