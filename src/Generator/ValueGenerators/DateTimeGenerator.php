<?php

declare(strict_types=1);

namespace Lord\Mother\Generator\ValueGenerators;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Lord\Mother\Contracts\ValueGeneratorInterface;
use Lord\Mother\Reflection\PropertyDefinition;
use Lord\Mother\Support\Options;

readonly class DateTimeGenerator implements ValueGeneratorInterface
{
    public int $from;

    public int $to;

    public function __construct(
        DateTimeInterface|int|string $from = 0,
        DateTimeInterface|int|string $to = 'now',
    ) {
        $this->from = $this->toTime($from);
        $this->to = $this->toTime($to);

        if ($this->from > $this->to) {
            throw new InvalidArgumentException('The "from" date must be earlier than or equal to the "to" date.');
        }
    }

    public function supports(PropertyDefinition $property, Options $options): bool
    {
        return is_string($property->type) && is_a($property->type, DateTimeInterface::class, true);
    }

    public function generate(PropertyDefinition $property, Options $options): DateTimeInterface
    {
        return $this->instance($property, mt_rand($this->from, $this->to));
    }

    protected function instance(PropertyDefinition $property, int $timestamp): DateTimeInterface
    {
        return match ($property->type) {
            DateTime::class => (new DateTime())->setTimestamp($timestamp),
            default => (new DateTimeImmutable())->setTimestamp($timestamp),
        };
    }

    protected function toTime(DateTimeInterface|int|string $value): int
    {
        if ($value instanceof DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_int($value)) {
            return $value;
        }

        return strtotime($value) ?: time();
    }
}
