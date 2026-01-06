# Mother

Mother is an Object Mother for PHP, providing nested object generation with minimal
configuration, attributes and extendable generators.

It is primarily intended for testing, but can also be used during early development
to return realistic data to the frontend before features are fully implemented.

### Installation

Installation via Composer - typically as a dev dependency, but can also be used in
production for feature flagged prototyping if desired.

```sh
composer require lord/mother --dev
```

### Basic usage

Objects can be generated a couple of ways:

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
    ->with('status', 'active')
    ->populateNulls()
    ->make(10);
```

> [!TIP]
> See the [example dir](example) for a more detailed demonstration of usage.

#### Test example

Mother makes Unit testing easier to write and maintain by simplifying the creation
of test data.

```php
<?php

it('crates a user', function () {
    $userRepoMock = mock(
        UserRepositoryInterface::class,
        static fn ($mock) => $mock
            ->shouldReceive('create')
            ->once()
            ->with('hire@aaron.codes', 'secret')
            ->andReturnUsing(static function (string $email) {
                // Use Mother to generate a UserData instance with the given email:
                return Mother::make(UserData::class, [
                    'email' => $email,
                ]);
            }
    );

    $sut = new CreateUserCommand($userRepoMock);

    $result = $sut->execute(
        email: 'hire@aaron.codes',
        password: 'secret',
    );

    expect($result)->toBeInstanceOf(UserData::class);
    expect($result->email)->toBe('hire@aaron.codes');
});
```

#### Mocking data example

Mother can also be used to generate mock data for use during development, such as
in repositories or services to return semi-realistic data before features are fully implemented.

```php
<?php

use Lord\Mother\Mother;

class UserRepository
{
    public function findUserByEmail(string $email): UserData
    {
        return Mother::make(UserData::class, [
            'email' => $email,
        ]);
    }
}
```

### Advanced Usage

#### Value generators

Creating a custom value generator is simple - just implement the
`Lord\Mother\Contracts\ValueGeneratorInterface`.

For example, here is a generator that creates UUID strings:

```php
<?php

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

class UuidGenerator implements ValueGeneratorInterface
{
    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->type === 'string' && $property->name === 'uuid';
    }

    public function generate(PropertyDefinition $property, Options $options): mixed
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
```

If you register this generator with Mother, as per the `supports` method, it will
be used whenever a property named `uuid` of type `string` is encountered:

```php
<?php

Mother::register(new UuidGenerator());
```

Alternatively, you can apply a generator to a specific property using the attribute:

```php
<?php

use Lord\Mother\Attributes\MotherUsing;

class ExampleData {
    #[MotherUsing(new UuidGenerator())]
    public string $id;
}
```

> [!NOTE]
> The `supports` method is not called when using the attribute

#### Object generators

Another approach is to create an object generator, and apply it to a class using the
`MotherUsing` attribute.

The contract for an object generator is very similar to a value generator, it
implements `Lord\Mother\Contracts\ObjectGeneratorInterface` - where the difference
is that the `generate` method returns `?object` instead of `mixed`.

```php
<?php

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

class UuidGenerator implements ValueGeneratorInterface
{
    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->name === '__class__' && $property->type === UuidValue::class;
    }

    public function generate(PropertyDefinition $property, Options $options): ?object
    {
        return new UuidValue(
            value: \Ramsey\Uuid\Uuid::uuid4()->toString(),
        );
    }
}
```


```php
<?php

use Lord\Mother\Attributes\MotherUsing;

#[MotherUsing(new UuidGenerator())]
class UuidValue {
    public function __construct(
        public string $value,
    ) {}
}
```

#### Options

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

#### Dependency injection

If you want to change how Mother works, you can provide your own container and
with your own classes registered. See `src/Contracts` for the interfaces that can be
implemented.

```php
<?php

Mother::resolveContainerUsing(fn () => $container);
```

### Testing

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
