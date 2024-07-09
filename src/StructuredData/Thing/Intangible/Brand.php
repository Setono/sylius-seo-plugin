<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

final class Brand extends StructuredData
{
    public function __construct(public ?string $name = null)
    {
        $this->type = 'Brand';
    }
}
