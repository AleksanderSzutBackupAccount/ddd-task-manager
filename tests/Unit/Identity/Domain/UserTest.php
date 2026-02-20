<?php

declare(strict_types=1);

namespace Tests\Unit\Identity\Domain;

use PHPUnit\Framework\TestCase;
use Src\Identity\Domain\User;
use Src\Identity\Domain\UserImportedEvent;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;

final class UserTest extends TestCase
{
    public function test_it_can_be_imported(): void
    {
        $name = new UserName('John Doe');
        $email = UserEmail::fromString('john@example.com');
        $externalId = new UserExternalId('1');

        $user = User::import($name, $email, $externalId);

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(UserId::class, $user->id);
        $this->assertEquals($name, $user->name);
        $this->assertEquals($email, $user->email);
        $this->assertEquals($externalId, $user->externalId);

        $events = $user->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserImportedEvent::class, $events[0]);
        $this->assertEquals($user->id, $events[0]->id);
    }
}
