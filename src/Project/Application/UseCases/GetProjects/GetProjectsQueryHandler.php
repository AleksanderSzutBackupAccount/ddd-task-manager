<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetProjects;

use Src\Project\Domain\ProjectRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetProjectsQuery, ProjectsResponse>
 */
final readonly class GetProjectsQueryHandler implements QueryHandlerInterface
{
    public function __construct(private ProjectRepository $repository) {}

    public function __invoke(QueryInterface $query): ProjectsResponse
    {
        return new ProjectsResponse($this->repository->getAssignedToUser($query->userId));
    }
}
