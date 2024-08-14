<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\Reference;
use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\ItemAvailability;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue\OfferShippingDetails;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue\QuantitativeValue;

class Offer extends StructuredData
{
    public function __construct(
        public ?string $url = null,
        public ?string $priceCurrency = null,
        public ?float $price = null,
        public ?\DateTimeInterface $priceValidUntil = null,
        public ?QuantitativeValue $eligibleQuantity = null,
        public ?string $itemCondition = null,
        public ?ItemAvailability $availability = null,
        public null|Reference|MerchantReturnPolicy $hasMerchantReturnPolicy = null,
        public null|Reference|OfferShippingDetails $shippingDetails = null,
    ) {
        $this->type = 'Offer';
    }
}
