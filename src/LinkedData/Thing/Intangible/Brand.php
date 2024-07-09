<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible;

use Setono\SyliusSEOPlugin\LinkedData\StructuredData;

final class Brand extends StructuredData
{
    public function __construct(public ?string $name = null)
    {
        $this->type = 'Brand';
    }
}
