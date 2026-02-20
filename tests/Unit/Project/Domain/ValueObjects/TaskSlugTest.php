<?php

declare(strict_types=1);

namespace Tests\Unit\Project\Domain\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Project\Domain\ValueObjects\TaskSlug;

final class TaskSlugTest extends TestCase
{
    public function test_it_should_create_valid_task_slug(): void
    {
        $slug = new TaskSlug('abcd-1');
        $this->assertSame('ABCD-1', $slug->value());
    }

    public function test_it_should_throw_exception_on_invalid_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TaskSlug('invalid-slug');
    }

    public function test_it_should_throw_exception_on_empty_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TaskSlug('');
    }
}
