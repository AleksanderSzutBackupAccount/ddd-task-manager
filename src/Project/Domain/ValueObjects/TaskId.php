<?php

declare(strict_types=1);

namespace Src\Project\Domain\ValueObjects;

use Src\Shared\Domain\ValueObjects\StringValueObject;

final readonly class TaskId extends StringValueObject
{
    public static function fromSlugAndUuid(string $slug, string $uuid): self
    {
        return new self(sprintf('%s-%s', $slug, $uuid));
    }
}
