<?php

declare(strict_types=1);

namespace Lord\Mother\Example\Generators;

use Lord\Mother\Contracts\ObjectGeneratorInterface;
use Lord\Mother\Example\DataTransferObjects\JobData;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

class JobGenerator implements ObjectGeneratorInterface
{
    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->name === '__class__' && $property->type === JobData::class;
    }

    public function generate(PropertyDefinition $property, Options $options): ?object
    {
        return new JobData(
            title: 'Software Engineer',
            department: 'Engineering',
        );
    }
}
