<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\MerchantReturnEnumeration;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\RefundTypeEnumeration;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\ReturnFeesEnumeration;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\ReturnMethodEnumeration;

class MerchantReturnPolicy extends StructuredData
{
    public ?array $applicableCountry = null;

    public ?string $returnPolicyCountry = null;

    public ?MerchantReturnEnumeration $returnPolicyCategory = null;

    public ?int $merchantReturnDays = null;

    public ?ReturnMethodEnumeration $returnMethod = null;

    public ?ReturnFeesEnumeration $returnFees = null;

    public ?RefundTypeEnumeration $refundType = null;

    public ?string $merchantReturnLink = null;
}
