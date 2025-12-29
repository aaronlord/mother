<?php

declare(strict_types=1);

namespace Lord\Mother\Example;

require __DIR__.'/../vendor/autoload.php';

use Lord\Mother\Example\DataTransferObjects\PersonData;
use Lord\Mother\Example\Generators\IdGenerator;
use Lord\Mother\Example\ValueObjects\NameValue;
use Lord\Mother\Mother;

// Bring your own container, if you like:
// Mother::resolveContainerUsing(
//     static fn (): \Psr\Container\ContainerInterface => \Illuminate\Container\Container::getInstance()
// );

Mother::register(new IdGenerator());

$person = Mother::make(PersonData::class, [
    'address.city' => 'New York',
]);

dump($person);

$builder = Mother::for(NameValue::class);

$names = $builder->populateNulls();

$b2 = Mother::for(PersonData::class);

$names = $builder->make(2);

dump($names);
