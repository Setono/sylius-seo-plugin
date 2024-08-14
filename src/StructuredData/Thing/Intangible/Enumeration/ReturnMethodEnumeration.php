<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration;

enum ReturnMethodEnumeration: string
{
    case KeepProduct = 'KeepProduct';
    case ReturnAtKiosk = 'ReturnAtKiosk';
    case ReturnByMail = 'ReturnByMail';
    case ReturnInStore = 'ReturnInStore';
}
