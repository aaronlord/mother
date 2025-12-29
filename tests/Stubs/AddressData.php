<?php

declare(strict_types=1);

namespace Lord\Mother\Tests\Stubs;

class AddressData
{
    public string $line1 = '123 Default St';

    public ?string $line2;

    public ?string $line3;

    public string $postcode;
}
