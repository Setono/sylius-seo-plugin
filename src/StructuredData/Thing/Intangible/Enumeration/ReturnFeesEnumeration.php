<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration;

enum ReturnFeesEnumeration: string
{
    case FreeReturn = 'FreeReturn';
    case OriginalShippingFees = 'OriginalShippingFees';
    case RestockingFees = 'RestockingFees';
    case ReturnFeesCustomerResponsibility = 'ReturnFeesCustomerResponsibility';
    case ReturnShippingFees = 'ReturnShippingFees';
}
