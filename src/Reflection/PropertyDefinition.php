<?php

declare(strict_types=1);

namespace Lord\Mother\Reflection;

class PropertyDefinition
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public string $name,
        public ?string $type,
        public bool $nullable = false,
        public bool $hasDefault = false,
        public mixed $default = null,
        public array $attributes = [],
    ) {
    }
}
