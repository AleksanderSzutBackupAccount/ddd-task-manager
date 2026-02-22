<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Response\Filters;

readonly class RangeFilterDefinition implements FilterDefinition
{
    public function __construct(
        private ?string $unit,
        private int|float|null $min,
        private int|float|null $max,
    ) {
    }

    public function toResponse(): array
    {
        return [
            'ui' => 'range',
            'unit' => $this->unit,
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
