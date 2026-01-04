<?php

declare(strict_types=1);

namespace Lord\Mother\Example\DataTransferObjects;

use Lord\Mother\Attributes\MotherUsing;
use Lord\Mother\Example\Generators\JobGenerator;

#[MotherUsing(new JobGenerator())]
final readonly class JobData
{
    public function __construct(
        public string $title,
        public string $department,
    ) {
    }
}
