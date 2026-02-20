<?php

declare(strict_types=1);

namespace Tests\Unit\Project\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use Src\Project\Domain\ValueObjects\TaskId;

final class TaskIdTest extends TestCase
{
    public function test_it_should_generate_valid_task_id(): void
    {
        $id = TaskId::generate();
        $this->assertMatchesRegularExpression('/^[0-9a-fA-F\-]{36}$/', $id->value());
    }

    public function test_it_should_create_from_string(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $id = new TaskId($uuid);
        $this->assertSame($uuid, $id->value());
    }

    public function test_it_should_throw_exception_on_invalid_uuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TaskId('not-a-uuid');
    }
}
