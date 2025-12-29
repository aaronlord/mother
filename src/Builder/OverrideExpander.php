<?php

declare(strict_types=1);

namespace Lord\Mother\Builder;

use Lord\Mother\Contracts\OverrideExpanderInterface;

class OverrideExpander implements OverrideExpanderInterface
{
    public function expand(array $overrides): array
    {
        $result = [];

        foreach ($overrides as $key => $value) {
            if (! is_string($key) || ! str_contains($key, '.')) {
                $result[$key] = $value;

                continue;
            }

            $segments = explode('.', $key);
            $current = &$result;

            foreach ($segments as $segment) {
                if (! isset($current[$segment]) || ! is_array($current[$segment])) {
                    $current[$segment] = [];
                }

                $current = &$current[$segment];
            }

            $current = $value;
        }

        return $result;
    }
}
