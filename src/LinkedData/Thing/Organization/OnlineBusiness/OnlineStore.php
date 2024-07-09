<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Organization\OnlineBusiness;

use Setono\SyliusSEOPlugin\LinkedData\LinkedData;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\MerchantReturnPolicy;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\StructuredValue\ContactPoint;

/**
 * See https://schema.org/OnlineStore
 */
class OnlineStore extends LinkedData
{
    public function __construct(
        public ?string $name = null,
        public ?string $url = null,
        public ?string $logo = null,
        public ?string $description = null,
        public ?ContactPoint $contactPoint = null,
        public ?ContactPoint\PostalAddress $address = null,
        public ?string $vatID = null,
        public ?string $iso6523Code = null,
        public ?MerchantReturnPolicy $hasMerchantReturnPolicy = null,
    ) {
        $this->type = 'OnlineStore';
        $this->context = 'https://schema.org';
    }
}
