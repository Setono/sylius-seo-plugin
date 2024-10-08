<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class ShippingDeliveryTime extends StructuredData
{
    public function __construct(
        public ?QuantitativeValue $handlingTime = null,
        public ?QuantitativeValue $transitTime = null,
    ) {
        $this->type = 'ShippingDeliveryTime';
    }
}
