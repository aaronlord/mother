<?php

declare(strict_types=1);

namespace Lord\Mother\Generator\ValueGenerators;

use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;
use RuntimeException;

readonly class StringGenerator implements ValueGeneratorInterface
{
    protected int $dictionaryLength;

    public function __construct(
        public string $dictionary = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        public int $minLength = 3,
        public int $maxLength = 20,
        public string $prefix = '',
    ) {
        $this->dictionaryLength = strlen($this->dictionary);
    }

    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return $property->type === 'string';
    }

    public function generate(PropertyDefinition $property, Options $options): string
    {
        $length = $this->length();

        $string = $this->prefix;

        for ($i = 0; $i < $length; $i++) {
            $string .= $this->char();
        }

        return $string;
    }

    protected function length(): int
    {
        $length = mt_rand($this->minLength, $this->maxLength);

        if ($this->prefix === '') {
            return $length;
        }

        $length -= mb_strlen($this->prefix);

        if ($length < 0) {
            throw new RuntimeException('Prefix is too long for the minimum length.');
        }

        return $length;
    }

    protected function char(): string
    {
        $index = mt_rand(0, $this->dictionaryLength - 1);

        return $this->dictionary[$index];
    }
}
