<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\CreateProject;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Shared\Domain\Bus\CommandInterface;

final readonly class CreateProjectCommand implements CommandInterface
{
    /**
     * @param string[] $userIds
     */
    public function __construct(
        public string $name,
        public string $slug,
        public UserId $userId,
        public array $userIds = [],
    ) {
    }
}
