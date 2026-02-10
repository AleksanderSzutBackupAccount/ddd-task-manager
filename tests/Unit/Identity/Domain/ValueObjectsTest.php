<?php

declare(strict_types=1);

namespace Tests\Unit\Identity\Domain;

use PHPUnit\Framework\TestCase;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;
use InvalidArgumentException;

final class ValueObjectsTest extends TestCase
{
    public function test_user_id_can_be_generated(): void
    {
        $id = UserId::generate();
        $this->assertInstanceOf(UserId::class, $id);
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid($id->value()));
    }

    public function test_user_email_validates_format(): void
    {
        $email = UserEmail::fromString('test@example.com');
        $this->assertEquals('test@example.com', $email->value());

        $this->expectException(InvalidArgumentException::class);
        UserEmail::fromString('invalid-email');
    }

    public function test_user_name_stores_value(): void
    {
        $name = new UserName('John Doe');
        $this->assertEquals('John Doe', $name->value());
    }

    public function test_user_external_id_stores_value(): void
    {
        $externalId = new UserExternalId('ext_123');
        $this->assertEquals('ext_123', $externalId->value());
    }
}
