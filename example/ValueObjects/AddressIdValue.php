<?php

declare(strict_types=1);

namespace Lord\Mother\Example\ValueObjects;

final readonly class AddressIdValue
{
    public function __construct(
        public int $id,
    ) {
    }
}
