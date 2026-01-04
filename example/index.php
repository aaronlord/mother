<?php

// Run this example with:
// mother --php=8.5 php example/index.php

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

// Register custom generators, if needed:
Mother::register(new IdGenerator());

// Create a PersonData object, overriding the city in the nested AddressData:
$person = Mother::make(PersonData::class, [
    'address.city' => 'New York',
]);

// p.s. Take a look at PersonData, and the other classes used in it, to see
// examples of the MotherUsing attribute, nested DTOs, Enums, Value Objects, and more.

dump($person);

// Results in:
//
// ^ Lord\Mother\Example\DataTransferObjects\PersonData^ {#55
//   +id: Lord\Mother\Example\ValueObjects\PersonIdValue^ {#31
//     +id: 200740
//   }
//   +name: Lord\Mother\Example\ValueObjects\NameValue^ {#25
//     +forename: "John"
//     +middlename: null
//     +surname: "Doe"
//   }
//   +gender: Lord\Mother\Example\Enums\GenderEnum^ {#24
//     +name: "MALE"
//     +value: "male"
//   }
//   +address: Lord\Mother\Example\DataTransferObjects\AddressData^ {#50
//     +id: Lord\Mother\Example\ValueObjects\AddressIdValue^ {#26
//       +id: 500641
//     }
//     +street: "7ZuNjueEjkt0bWrJNG"
//     +city: "New York"
//     +postcode: null
//     +country: "gnu055dtYTHhUNtOi"
//   }
//   +age: -741559
//   +allowance: 540260.0
//   +dateOfBirth: DateTimeImmutable @646525131 {#29
//     date: 1990-06-27 22:18:51.0 UTC (+00:00)
//   }
// }

// Use the builder to create multiple NameValue objects, with more control:
$names = Mother::for(NameValue::class)
    ->with('surname', 'Smith')
    ->populateNulls()
    ->make(2);

dump($names);

// Results in:
//
// ^ array:2 [
//   0 => Lord\Mother\Example\ValueObjects\NameValue^ {#63
//     +forename: "jwOXucsBqDVfb"
//     +middlename: "0aYFAJdoclY4T"
//     +surname: "Smith"
//   }
//   1 => Lord\Mother\Example\ValueObjects\NameValue^ {#62
//     +forename: "H2HYoV5okM"
//     +middlename: "bCOUM10ivlGZTjsym"
//     +surname: "Smith"
//   }
// ]
