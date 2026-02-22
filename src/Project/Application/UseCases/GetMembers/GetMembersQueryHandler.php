<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetMembers;

use Src\Project\Domain\MemberRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;

/**
 * @implements QueryHandlerInterface<GetMembersQuery, MembersResponse>
 */
final readonly class GetMembersQueryHandler implements QueryHandlerInterface
{
    public function __construct(private MemberRepository $repository)
    {
    }

    public function __invoke(GetMembersQuery $query): MembersResponse
    {
        return new MembersResponse($this->repository->getMembers($query->projectId));
    }
}
