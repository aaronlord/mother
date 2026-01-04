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
        // The IdGenerator registered in index.php will be used for this:
        public PersonIdValue $id,

        // Opt to use a specific generator for this property:
        #[MotherUsing(new NameGenerator())]
        public string|NameValue $name,

        // The nested DTO will be created automatically.
        // Note: AddressData is defined using public properties instead of a constructor.
        public AddressData $address,

        // Note: JobData declares a customer gnerator using the MotherUsing attribute
        // on the class itself.
        public JobData $job,

        // Scalars, Enums, DateTimeInterface, etc. are all supported out of the box:
        public int $age,
        public float $allowance,
        public GenderEnum $gender,
        public DateTimeInterface $dateOfBirth,
    ) {
    }
}
