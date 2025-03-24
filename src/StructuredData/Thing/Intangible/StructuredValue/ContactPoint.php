<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class ContactPoint extends StructuredData
{
    public ?string $email = null;

    public ?string $telephone = null;
}
