<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetMembers;

use Src\Identity\Domain\ValueObjects\UserId;
use Src\Project\Domain\ValueObjects\ProjectId;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryInterface<MembersResponse>
 */
final readonly class GetMembersQuery implements QueryInterface
{
    public function __construct(public ProjectId $projectId) {}
}
