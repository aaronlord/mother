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

```php
<?php

use Lord\Mother\Mother;

# Generate an instance of UserData:
$user = Mother::make(UserData::class);

# Generate with specific properties overridden:
$user = Mother::make(UserData::class, [
    'name' => 'Jane Doe',
    'address.city' => 'London',
]);


# Generate multiple instances with the builder:
$users = Mother::for(UserData::class)
    ->with([
        'status' => 'active',
    ])
    ->populateNulls()
    ->make(10);
```
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

Or apply a generator to an entire class:

```php
<?php

use Lord\Mother\Attributes\MotherUsing;

#[MotherUsing(new ExampleGenerator())]
class ExampleData {
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
    new Options(
        maxDepth: 2,
    )
);

// Or using an array:

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

### Dependency injection

If you want to change how Mother works, you can provide your own container and
with your own classes registered. See `src/Contracts` for the interfaces that can be
implemented.

```php
<?php

Mother::resolveContainerUsing(fn () => $container);
```

## Testing

A shell command `/bin/mother` is provided to aid with development. Here are some
examples of how to use it:

```sh
# Run the entire test suite
mother test

# Just run static analysis
mother phpstan

# Run a specific test, on a specific PHP version
mother --php=8.3 pest tests/Feature/MotherTest.php
```
