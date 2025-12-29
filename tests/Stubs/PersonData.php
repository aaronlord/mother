<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Stubs;

final readonly class PersonData
{
    public function __construct(
        public string $name,
        public int $age,
        public AddressData $address,
        public JobData $job,
    ) {
    }
}
