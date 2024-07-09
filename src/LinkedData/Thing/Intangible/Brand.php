<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible;

use Setono\SyliusSEOPlugin\LinkedData\LinkedData;

final class Brand extends LinkedData
{
    public function __construct(public ?string $name = null)
    {
        $this->type = 'Brand';
    }
}
