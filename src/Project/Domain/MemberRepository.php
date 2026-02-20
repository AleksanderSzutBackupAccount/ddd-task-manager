<?php

declare(strict_types=1);

namespace Src\Project\Domain;

use Src\Project\Domain\Collections\MemberCollection;
use Src\Project\Domain\ValueObjects\ProjectId;

interface MemberRepository
{
    public function getMembers(ProjectId $projectId): MemberCollection;
}
