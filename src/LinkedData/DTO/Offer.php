<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\DTO;

class Offer extends LinkedData
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
