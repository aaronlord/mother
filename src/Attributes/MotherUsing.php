<?php

declare(strict_types=1);

namespace Lord\Mother\Attributes;

use Attribute;
use Lord\Mother\Contracts\ValueGeneratorInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class MotherUsing
{
    /**
     * @param ValueGeneratorInterface $class
     */
    public function __construct(
        public ValueGeneratorInterface $class,
    ) {
    }
}
