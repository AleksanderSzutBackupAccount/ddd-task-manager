<?php

declare(strict_types=1);

namespace Src\Project\Application\UseCases\GetMembers;

use Src\Project\Domain\MemberRepository;
use Src\Project\Domain\ProjectRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<GetMembersQuery, MembersResponse>
 */
final readonly class GetMembersQueryHandler implements QueryHandlerInterface
{
    public function __construct(private MemberRepository $repository) {}

    public function __invoke(QueryInterface $query): MembersResponse
    {
        return new MembersResponse($this->repository->getMembers($query->projectId));
    }
}
