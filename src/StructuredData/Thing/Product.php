<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Brand;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\ItemAvailability;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer\AggregateOffer;

/**
 * See
 * - https://developers.google.com/search/docs/appearance/structured-data/merchant-listing
 * - https://schema.org/Product
 */
class Product extends StructuredData
{
    public function __construct(
        public ?string $name = null,

        /** @var string|list<string>|null The URL of the item's image. This can be a URL or an array of URLs. */
        public string|array|null $image = null,
        public ?string $description = null,
        public ?string $sku = null,
        public ?string $mpn = null,
        public ?Brand $brand = null,
        public ?string $color = null,
        public ?string $size = null,
        public Offer|AggregateOffer|null $offers = null,
        public ?ItemAvailability $availability = null,
    ) {
        $this->type = 'Product';
        $this->context = 'https://schema.org';
    }
}
