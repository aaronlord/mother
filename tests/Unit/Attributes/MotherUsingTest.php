<?php

declare(strict_types=1);

namespace Tests\Unit\Attributes;

use Lord\Mother\Attributes\MotherUsing;
use Lord\Mother\Generator\ValueGenerators\StringGenerator;
use Lord\Mother\Mother;

mutates(MotherUsing::class);

it('can instantiate attribute on property', function () {
    class AttributeOnPropertyDataStub
    {
        #[MotherUsing(new StringGenerator())]
        public mixed $value;
    }

    $data = Mother::make(AttributeOnPropertyDataStub::class);

    expect($data->value)->toBeString();
});

it('can instantiate attribute on parameter', function () {
    class AttributeOnParameterDataStub
    {
        public function __construct(
            #[MotherUsing(new StringGenerator())]
            public mixed $value,
        ) {
        }
    }

    $data = Mother::make(AttributeOnParameterDataStub::class);

    expect($data->value)->toBeString();
});
