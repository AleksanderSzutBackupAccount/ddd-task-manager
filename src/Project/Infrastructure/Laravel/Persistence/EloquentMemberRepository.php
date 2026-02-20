<?php

declare(strict_types=1);

namespace Src\Project\Infrastructure\Laravel\Persistence;

use Src\Project\Domain\Collections\MemberCollection;
use Src\Project\Domain\Member;
use Src\Project\Domain\MemberRepository;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Project\Infrastructure\Laravel\Models\ProjectModel;

final class EloquentMemberRepository implements MemberRepository
{
    public function getMembers(ProjectId $projectId): MemberCollection
    {
        /** @var ProjectModel $project */
        $project = ProjectModel::query()->with('users')->findOrfail((string) $projectId);

        /** @var Member[] $members */
        $members = $project->users
            ->map(fn ($member) => new Member($member->id, $member->name))
            ->toArray();

        return new MemberCollection($members);
    }
}
