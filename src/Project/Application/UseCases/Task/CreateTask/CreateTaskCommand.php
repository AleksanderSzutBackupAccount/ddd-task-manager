<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\Task\CreateTask;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Shared\Domain\Bus\CommandInterface;

final readonly class CreateTaskCommand implements CommandInterface
{
    public function __construct(
        public string $projectSlug,
        public string $name,
        public string $description,
        public ?UserId $assignedUserId,
    ) {
    }
}
