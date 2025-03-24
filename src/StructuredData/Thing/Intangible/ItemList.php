<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\ItemListOrderType;

class ItemList extends StructuredData
{
    /** @var list<ListItem> */
    public array $itemListElement = [];

    public ?int $numberOfItems = null;

    public ?ItemListOrderType $itemListOrder = null;
}
