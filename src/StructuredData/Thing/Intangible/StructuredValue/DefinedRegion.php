<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class DefinedRegion extends StructuredData
{
    public function __construct(
        public ?string $addressCountry = null,
    ) {
        $this->type = 'DefinedRegion';
    }
}
