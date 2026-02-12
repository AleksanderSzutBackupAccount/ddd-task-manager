<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\CreateProject;

use Src\Shared\Domain\Bus\CommandInterface;

final readonly class CreateProjectCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $slug
    ) {}
}
