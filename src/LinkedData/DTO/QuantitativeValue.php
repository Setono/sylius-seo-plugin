<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\DTO;

final class QuantitativeValue extends LinkedData
{
    public function __construct(
        public ?float $maxValue = null,
        public ?float $minValue = null,
        public ?float $value = null,
        public ?string $unitCode = null,
        public ?string $unitText = null,
    ) {
        $this->type = 'QuantitativeValue';
    }
}
