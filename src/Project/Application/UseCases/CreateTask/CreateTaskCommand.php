<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\CreateTask;

use Src\Shared\Domain\Bus\CommandInterface;

final readonly class CreateTaskCommand implements CommandInterface
{
    public function __construct(
        public string $projectSlug,
        public string $name,
        public string $description,
        public ?string $assignedUserId = null
    ) {}
}
