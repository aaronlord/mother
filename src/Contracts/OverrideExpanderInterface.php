<?php

declare(strict_types=1);

namespace Lord\Mother\Contracts;

interface OverrideExpanderInterface
{
    /**
     * Expands dot notation keys in the overrides array into nested arrays.
     *
     * @param array<int|string, mixed> $overrides
     * @return array<int|string, mixed>
     */
    public function expand(array $overrides): array;
}
