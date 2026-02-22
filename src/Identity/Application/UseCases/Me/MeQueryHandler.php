<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\Me;

use Src\Identity\Domain\Exceptions\UserNotFoundException;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;

/**
 * @implements QueryHandlerInterface<MeQuery, User>
 */
final readonly class MeQueryHandler implements QueryHandlerInterface
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(MeQuery $query): User
    {
        /** @var MeQuery $query */
        $user = $this->repository->find(new UserId($query->userId));

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
