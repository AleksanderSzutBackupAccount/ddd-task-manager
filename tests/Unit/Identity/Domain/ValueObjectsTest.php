<?php

declare(strict_types=1);

namespace Tests\Unit\Identity\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Identity\Domain\ValueObjects\UserEmail;
use Src\Identity\Domain\ValueObjects\UserExternalId;
use Src\Identity\Domain\ValueObjects\UserId;
use Src\Identity\Domain\ValueObjects\UserName;

final class ValueObjectsTest extends TestCase
{
    public function test_user_id_can_be_generated(): void
    {
        $id = UserId::generate();
        $this->assertInstanceOf(UserId::class, $id);
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid($id->value()));
    }

    public function test_user_id_throws_exception_for_invalid_uuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UserId('invalid-uuid');
    }

    public function test_user_email_validates_format(): void
    {
        $email = UserEmail::fromString('test@example.com');
        $this->assertEquals('test@example.com', $email->value());
    }

    /**
     * @dataProvider invalidEmailsProvider
     */
    public function test_user_email_throws_exception_for_invalid_format(string $invalidEmail): void
    {
        $this->expectException(InvalidArgumentException::class);
        UserEmail::fromString($invalidEmail);
    }

    public static function invalidEmailsProvider(): array
    {
        return [
            ['invalid-email'],
            ['test@'],
            ['@example.com'],
            ['test.example.com'],
            [''],
        ];
    }

    public function test_user_name_stores_value(): void
    {
        $name = new UserName('John Doe');
        $this->assertEquals('John Doe', $name->value());
    }

    public function test_user_name_can_be_empty(): void
    {
        $name = new UserName('');
        $this->assertEquals('', $name->value());
    }

    public function test_user_name_can_be_very_long(): void
    {
        $longName = str_repeat('a', 1000);
        $name = new UserName($longName);
        $this->assertEquals($longName, $name->value());
    }

    public function test_user_external_id_stores_value(): void
    {
        $externalId = new UserExternalId('ext_123');
        $this->assertEquals('ext_123', $externalId->value());
    }
}
