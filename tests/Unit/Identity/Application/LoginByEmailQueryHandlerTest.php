<?php

declare(strict_types=1);

namespace Tests\Unit\Identity\Application;

use PHPUnit\Framework\TestCase;
use Src\Identity\Application\Ports\TokenGeneratorInterface;
use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQuery;
use Src\Identity\Application\UseCases\LoginByEmail\LoginByEmailQueryHandler;
use Src\Identity\Domain\Exceptions\UserNotFoundException;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserRepository;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;

final class LoginByEmailQueryHandlerTest extends TestCase
{
    private UserRepository $repository;

    private TokenGeneratorInterface $tokenGenerator;

    private LoginByEmailQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $this->handler = new LoginByEmailQueryHandler($this->repository, $this->tokenGenerator);
    }

    public function test_it_generates_token_for_existing_user(): void
    {
        $emailStr = 'test@example.com';
        $user = new User(
            UserId::generate(),
            new UserName('Test User'),
            UserEmail::fromString($emailStr),
            new UserExternalId('1')
        );

        $this->repository->expects($this->once())
            ->method('findByEmail')
            ->with($this->callback(fn ($email) => (string) $email === $emailStr))
            ->willReturn($user);

        $this->tokenGenerator->expects($this->once())
            ->method('generate')
            ->with($user)
            ->willReturn('generated-token');

        $query = new LoginByEmailQuery($emailStr);
        $token = $this->handler->__invoke($query);

        $this->assertEquals('generated-token', $token);
    }

    public function test_it_throws_exception_when_user_not_found(): void
    {
        $emailStr = 'nonexistent@example.com';

        $this->repository->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $this->tokenGenerator->expects($this->never())
            ->method('generate');

        $query = new LoginByEmailQuery($emailStr);

        $this->expectException(UserNotFoundException::class);

        $this->handler->__invoke($query);
    }
}
