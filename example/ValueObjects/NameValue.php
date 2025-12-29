<?php

declare(strict_types=1);

namespace Lord\Mother\Example\ValueObjects;

final readonly class NameValue
{
    public function __construct(
        public string $forename,
        public ?string $middlename,
        public string $surname,
    ) {
    }
}
