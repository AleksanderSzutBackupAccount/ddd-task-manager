<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\CreateProject;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\Project;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Application\Bus\CommandHandlerInterface;

final readonly class CreateProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(private ProjectRepository $projects)
    {
    }

    public function __invoke(CreateProjectCommand $command): void
    {
        $project = Project::create(ProjectId::generate(), $command->name, new ProjectSlug($command->slug));

        $project->assignUser($command->userId);

        foreach ($command->userIds as $userId) {
            $project->assignUser(new UserId($userId));
        }

        $this->projects->save($project);
    }
}
