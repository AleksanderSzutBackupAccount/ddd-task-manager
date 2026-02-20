<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\GetUsers;

use Src\Identity\Domain\UserRepository;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;

/**
 * @implements QueryHandlerInterface<GetUsersQuery, UserResponse>
 */
final readonly class GetUsersQueryHandler implements QueryHandlerInterface
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(GetUsersQuery $query): UserResponse
    {
        $users = $this->repository->all();

        return new UserResponse($users);
    }
}
