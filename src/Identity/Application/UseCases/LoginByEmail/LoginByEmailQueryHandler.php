<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\LoginByEmail;

use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Domain\Exceptions\UserNotFoundException;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;

/**
 * @implements QueryHandlerInterface<LoginByEmailQuery, string>
 */
final readonly class LoginByEmailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepository $repository,
        private TokenGeneratorInterface $tokenGenerator,
    ) {
    }

    public function __invoke(LoginByEmailQuery $query): string
    {
        $user = $this->repository->findByEmail(UserEmail::fromString($query->email));

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $this->tokenGenerator->generate($user);
    }
}
