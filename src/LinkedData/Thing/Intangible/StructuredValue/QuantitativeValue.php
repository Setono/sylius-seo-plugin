<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\LinkedData\StructuredData;

final class QuantitativeValue extends StructuredData
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
