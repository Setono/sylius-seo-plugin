<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration;

enum ItemListOrderType: string
{
    case ItemListOrderAscending = 'ItemListOrderAscending';
    case ItemListOrderDescending = 'ItemListOrderDescending';
    case ItemListUnordered = 'ItemListUnordered';
}
