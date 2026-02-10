<?php

declare(strict_types=1);

namespace Src\Identity\Application\UseCases\LoginByEmail;

use Src\Identity\Domain\Exceptions\UserNotFoundException;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Shared\Application\Auth\TokenGeneratorInterface;
use Src\Shared\Application\Bus\Query\QueryHandlerInterface;
use Src\Shared\Application\Bus\Query\QueryInterface;

/**
 * @implements QueryHandlerInterface<LoginByEmailQuery, string>
 */
final readonly class LoginByEmailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepository $repository,
        private TokenGeneratorInterface $tokenGenerator
    ) {}

    public function __invoke(QueryInterface $query): string
    {
        /** @var LoginByEmailQuery $query */
        $user = $this->repository->findByEmail(UserEmail::fromString($query->email));

        if ($user === null) {
            throw new UserNotFoundException;
        }

        return $this->tokenGenerator->generate($user->id->value, ['email' => $user->email->value]);
    }
}
