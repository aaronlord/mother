<?php

declare(strict_types=1);

namespace Lord\Mother\Support;

class Options
{
    /** @var array<string,int> */
    protected array $depthMap = [];

    public function __construct(
        public int $maxDepth = 3,
        public bool $populateNulls = false,
        public bool $respectDefaultValues = true,
    ) {
    }

    /**
     * @param array<int|string, mixed> $options
     */
    public static function from(array $options): self
    {
        $instance = new self();

        foreach ($options as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }

    public function depth(string $class): int
    {
        return $this->depthMap[$class] ?? 0;
    }

    public function enter(string $class): self
    {
        $clone = clone $this;

        $clone->depthMap[$class] = ($this->depthMap[$class] ?? 0) + 1;

        return $clone;
    }
}
