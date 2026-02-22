<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Symfony\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Src\Project\Domain\Collections\MemberCollection;
use Src\Project\Domain\Member;
use Src\Project\Domain\MemberRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Infrastructure\Symfony\Entity\ProjectEntity;

final readonly class MemberSymfonyRepository implements MemberRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getMembers(ProjectId $projectId): MemberCollection
    {
        /** @var ProjectEntity|null $project */
        $project = $this->entityManager->find(ProjectEntity::class, $projectId->value());

        if (!$project) {
            return new MemberCollection([]);
        }

        $members = [];
        foreach ($project->getUsers() as $user) {
            $members[] = new Member(new \Src\Identity\Domain\ValueObjects\UserId($user->getId()), $user->getName());
        }

        return new MemberCollection($members);
    }
}
