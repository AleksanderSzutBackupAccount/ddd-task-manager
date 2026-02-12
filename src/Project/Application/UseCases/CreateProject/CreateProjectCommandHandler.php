<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\CreateProject;

use Src\Project\Domain\Project;
use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Domain\ValueObjects\ProjectSlug;
use Src\Shared\Application\Bus\CommandHandlerInterface;
use Src\Shared\Domain\Bus\CommandInterface;

final readonly class CreateProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(private ProjectRepository $projects) {}

    public function handle(CommandInterface $command): void
    {
        /** @var CreateProjectCommand $command */
        $project = Project::create(ProjectId::generate(), $command->name, new ProjectSlug($command->slug));
        $this->projects->save($project);
    }
}
