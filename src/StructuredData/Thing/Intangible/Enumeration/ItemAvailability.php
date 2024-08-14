<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration;

enum ItemAvailability: string
{
    case BackOrder = 'BackOrder';
    case Discontinued = 'Discontinued';
    case InStock = 'InStock';
    case InStoreOnly = 'InStoreOnly';
    case LimitedAvailability = 'LimitedAvailability';
    case OnlineOnly = 'OnlineOnly';
    case OutOfStock = 'OutOfStock';
    case PreOrder = 'PreOrder';
    case PreSale = 'PreSale';
    case SoldOut = 'SoldOut';
}
