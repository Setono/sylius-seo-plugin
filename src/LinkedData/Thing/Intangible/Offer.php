<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible;

use Setono\SyliusSEOPlugin\LinkedData\StructuredData;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\StructuredValue\QuantitativeValue;

class Offer extends StructuredData
{
    public function __construct(
        public ?string $url = null,
        public ?string $priceCurrency = null,
        public ?float $price = null,
        public ?\DateTimeInterface $priceValidUntil = null,
        public ?QuantitativeValue $eligibleQuantity = null,
        public ?string $itemCondition = null,
        public ?string $availability = null,
    ) {
        $this->type = 'Offer';
    }
}
