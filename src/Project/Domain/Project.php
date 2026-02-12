<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Domain\Aggregate\AggregateRoot;

final class Project extends AggregateRoot
{
    /**
     * @param string[] $userIds
     */
    public function __construct(
        private readonly ProjectId $id,
        private string $name,
        private readonly ProjectSlug $slug,
        private array $userIds = []
    ) {}

    public static function create(ProjectId $id, string $name, ProjectSlug $slug): self
    {
        return new self($id, $name, $slug);
    }

    public function id(): ProjectId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): ProjectSlug
    {
        return $this->slug;
    }

    /**
     * @return string[]
     */
    public function userIds(): array
    {
        return $this->userIds;
    }

    public function isUserAssigned(string $userId): bool
    {
        return in_array($userId, $this->userIds, true);
    }

    public function assignUser(string $userId): void
    {
        if (!$this->isUserAssigned($userId)) {
            $this->userIds[] = $userId;
        }
    }
}
