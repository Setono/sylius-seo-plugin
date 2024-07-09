<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\StructuredValue\ContactPoint;

use Setono\SyliusSEOPlugin\LinkedData\StructuredData;

class PostalAddress extends StructuredData
{
    public function __construct(
        public ?string $streetAddress = null,
        public ?string $addressLocality = null,
        public ?string $addressCountry = null,
        public ?string $addressRegion = null,
        public ?string $postalCode = null,
    ) {
        $this->type = 'PostalAddress';
    }
}
