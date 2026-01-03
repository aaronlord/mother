# Mother

Mother is a small PHP library for generating objects populated with fake or
placeholder data.

It is primarily intended for testing, but can also be used during early development
to return realistic data to the frontend before features are fully implemented.

The design is inspired by the Object Mother pattern, with a focus on:

- type-aware generation
- sensible defaults
- deep object graphs
- minimal configuration

### Installation

```sh
composer require lord/mother --dev
```

### Basic usage

Generate a fully populated object:

```php
<?php

use Lord\Mother\Mother;

$user = Mother::make(UserData::class);
```

Override specific properties:

```php
<?php

$user = Mother::make(UserData::class, [
    'name' => 'Jane Doe',
    'address.city' => 'London',
]);
```
Generate multiple instances:

```php
<?php

$users = Mother::for(UserData::class)->make(10);
```

### Nested objects

Mother will automatically generate nested objects when it encounters class-typed properties:

```php
<?php

class PersonData {
    public function __construct(
        public string $name,
        public AddressData $address,
    ) {}
}

$person = Mother::make(PersonData::class);
```

Overrides may be provided as nested arrays or dot-notation keys.

### Custom generators

You can register your own value generators:

```php
<?php

Mother::register(new CustomStringGenerator());
```

Or apply them at a property or parameter level using attributes:

```php
<?php

use Lord\Mother\Attributes\MotherUsing;

class ExampleData {
    #[MotherUsing(new StringGenerator())]
    public string $value;
}
```

### Options

Generation behaviour can be customised:

```php
<?php

Mother::make(
    UserData::class,
    [],
    [
        'maxDepth' => 2,
        'populateNulls' => false,
        'respectDefaultValues' => true,
    ]
);
```

Dependency injection

Mother uses a container internally but allows consumers to provide their own:

```php
<?php

Mother::resolveContainerUsing(fn () => $container);
```

This makes it easy to integrate with existing frameworks.
