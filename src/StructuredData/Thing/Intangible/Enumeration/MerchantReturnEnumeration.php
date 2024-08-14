<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration;

enum MerchantReturnEnumeration: string
{
    case MerchantReturnFiniteReturnWindow = 'MerchantReturnFiniteReturnWindow';
    case MerchantReturnNotPermitted = 'MerchantReturnNotPermitted';
    case MerchantReturnUnlimitedWindow = 'MerchantReturnUnlimitedWindow';
    case MerchantReturnUnspecified = 'MerchantReturnUnspecified';
}
