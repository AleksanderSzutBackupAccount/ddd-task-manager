<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetMembers;

use Src\Project\Domain\Collections\MemberCollection;
use Src\Project\Domain\Collections\ProjectCollection;
use Src\Project\Domain\Member;
use Src\Project\Domain\Project;

final readonly class MembersResponse
{
    public function __construct(private MemberCollection $members) {}

    /**
     * @return array<mixed>[]
     */
    public function toResponse(): array
    {
        return $this->members->map(static fn (Member $member) => [
            'id' => $member->id,
            'name' => $member->name,
        ]);
    }
}
