<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetAllTasks;

use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<array>
 */
final readonly class GetAllTasksQuery implements QueryInterface {}
