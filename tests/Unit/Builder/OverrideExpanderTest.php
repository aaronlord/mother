<?php

declare(strict_types=1);

namespace Tests\Unit\Builder;

use Lord\Mother\Builder\OverrideExpander;
use Lord\Mother\Contracts\OverrideExpanderInterface;

mutates(OverrideExpander::class);

describe('di', function () {
    it('is an instance of OverrideExpanderInterface', function () {
        $sut = new OverrideExpander();

        expect($sut)->toBeInstanceOf(OverrideExpanderInterface::class);
    });
});

describe('expand', function () {
    it('should expand dot notation keys into nested arrays', function () {
        $sut = new OverrideExpander();

        $result = $sut->expand([
            'name' => 'John Doe',
            'job.title' => 'Developer',
            'job.languages' => ['PHP', 'JavaScript'],
            'address.line1' => '123 Main St',
            'address.line2' => 'Anytown',
        ]);

        expect($result)->toEqual([
            'name' => 'John Doe',
            'job' => [
                'title' => 'Developer',
                'languages' => ['PHP', 'JavaScript'],
            ],
            'address' => [
                'line1' => '123 Main St',
                'line2' => 'Anytown',
            ],
        ]);
    });
});
