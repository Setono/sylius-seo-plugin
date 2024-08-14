<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration;

enum RefundTypeEnumeration: string
{
    case ExchangeRefund = 'ExchangeRefund';
    case FullRefund = 'FullRefund';
    case StoreCreditRefund = 'StoreCreditRefund';
}
