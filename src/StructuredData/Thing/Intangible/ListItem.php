<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\Thing;

class ListItem extends Thing
{
    public ?string $item = null;

    public ?int $position = null;
}
