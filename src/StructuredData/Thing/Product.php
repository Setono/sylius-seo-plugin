<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing;

use Setono\SyliusSEOPlugin\StructuredData\Thing;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Brand;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer\AggregateOffer;

/**
 * See
 * - https://developers.google.com/search/docs/appearance/structured-data/merchant-listing
 * - https://schema.org/Product
 */
class Product extends Thing
{
    public ?string $sku = null;

    public ?string $mpn = null;

    public ?string $gtin = null;

    public ?Brand $brand = null;

    public ?string $color = null;

    public ?string $size = null;

    public Offer|AggregateOffer|null $offers = null;
}
