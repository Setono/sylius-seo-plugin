<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\DTO;

final class Brand extends LinkedData
{
    public function __construct(public ?string $name = null)
    {
        $this->type = 'Brand';
    }
}
