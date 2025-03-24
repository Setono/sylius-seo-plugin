<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Organization\OnlineBusiness;

use Setono\SyliusSEOPlugin\StructuredData\Thing;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\MerchantReturnPolicy;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue\ContactPoint;

/**
 * See https://schema.org/OnlineStore
 */
class OnlineStore extends Thing
{
    public ?string $logo = null;

    public ?string $description = null;

    public ?ContactPoint $contactPoint = null;

    public ?ContactPoint\PostalAddress $address = null;

    public ?string $vatID = null;

    public ?string $iso6523Code = null;

    public ?MerchantReturnPolicy $hasMerchantReturnPolicy = null;
}
