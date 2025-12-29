<?php

declare(strict_types=1);

namespace Lord\Mother\Example\DataTransferObjects;

use DateTimeInterface;
use Lord\Mother\Attributes\MotherUsing;
use Lord\Mother\Example\Enums\GenderEnum;
use Lord\Mother\Example\Generators\NameGenerator;
use Lord\Mother\Example\ValueObjects\NameValue;
use Lord\Mother\Example\ValueObjects\PersonIdValue;

final readonly class PersonData
{
    public function __construct(
        public PersonIdValue $id,
        #[MotherUsing(new NameGenerator())]
        public string|NameValue $name,
        public GenderEnum $gender,
        public AddressData $address,
        public int $age,
        public float $allowance,
        public DateTimeInterface $dateOfBirth,
    ) {
    }
}
