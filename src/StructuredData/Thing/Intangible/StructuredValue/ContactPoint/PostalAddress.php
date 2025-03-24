<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue\ContactPoint;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class PostalAddress extends StructuredData
{
    public ?string $streetAddress = null;

    public ?string $addressLocality = null;

    public ?string $addressCountry = null;

    public ?string $addressRegion = null;

    public ?string $postalCode = null;
}
