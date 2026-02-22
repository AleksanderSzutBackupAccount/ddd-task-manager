<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony;

use Src\Project\Domain\ProjectRepository;
use Src\Project\Domain\TaskReadRepository;
use Src\Project\Domain\TaskWriteRepository;
use Src\Project\Infrastructure\Symfony\Persistence\EventSourcedTaskSymfonyWriteRepository;
use Src\Project\Infrastructure\Symfony\Persistence\ProjectSymfonyRepository;
use Src\Project\Infrastructure\Symfony\Persistence\TaskSymfonyReadRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ProjectSymfonyProvider
{
    public function load(ContainerBuilder $container): void
    {
        $container->setAlias(ProjectRepository::class, ProjectSymfonyRepository::class);
        $container->setAlias(TaskReadRepository::class, TaskSymfonyReadRepository::class);
        $container->setAlias(TaskWriteRepository::class, EventSourcedTaskSymfonyWriteRepository::class);
    }
}
