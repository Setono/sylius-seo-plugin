<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class MonetaryAmount extends StructuredData
{
    public ?string $currency = null;

    public ?float $value = null;
}
