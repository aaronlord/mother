<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Stubs;

class JobData
{
    public function __construct(
        public string $title,
        public float $salary,
        public ?PersonData $manager,
        public string $company = 'Acme Corp',
    ) {
    }
}
