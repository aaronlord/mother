<?php

declare(strict_types=1);

namespace Lord\Mother\Example\DataTransferObjects;

use Lord\Mother\Example\ValueObjects\AddressIdValue;

class AddressData
{
    public AddressIdValue $id;

    public string $street;

    public string $city;

    public ?string $postcode;

    public string $country;
}
