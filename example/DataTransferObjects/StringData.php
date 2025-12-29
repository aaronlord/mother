<?php

declare(strict_types=1);

namespace Tests\Stubs\DataTransferObjects;

class StringData
{
    public function __construct(
        public string $string,
        public ?string $nullableString,
        public string $stringWithStringDefault = 'default',
        public ?string $nullableStringWithStringDefault = 'default',
        public ?string $nullableStringWithNullDefault = null,
    ) {
    }
}
