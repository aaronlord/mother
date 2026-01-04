<?php

declare(strict_types=1);

namespace Lord\Mother\Attributes;

use Attribute;
use Lord\Mother\Contracts\ValueGeneratorInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
final class MotherUsing
{
    public function __construct(
        public ValueGeneratorInterface $class,
    ) {
    }
}
