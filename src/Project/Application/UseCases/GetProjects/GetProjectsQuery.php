<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetProjects;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<ProjectsResponse>
 */
final readonly class GetProjectsQuery implements QueryInterface
{
    public function __construct(public UserId $userId) {}
}
