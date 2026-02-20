<?php

declare(strict_types=1);

namespace Src\Project\Domain\Collections;

use Src\Project\Domain\Member;
use Src\Shared\Domain\Collection\Collection;

/**
 * @extends Collection<Member>
 */
final class MemberCollection extends Collection
{
    protected function type(): string
    {
        return Member::class;
    }
}
