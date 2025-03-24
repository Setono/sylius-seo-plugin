<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class OfferShippingDetails extends StructuredData
{
    public ?MonetaryAmount $shippingRate = null;

    /** @var DefinedRegion|list<DefinedRegion>|null */
    public null|DefinedRegion|array $shippingDestination = null;

    public ?ShippingDeliveryTime $deliveryTime = null;
}
