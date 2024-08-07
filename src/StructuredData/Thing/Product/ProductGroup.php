<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Product;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Brand;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;

/**
 * See
 * - https://developers.google.com/search/docs/appearance/structured-data/product-variants
 * - https://schema.org/ProductGroup
 */
final class ProductGroup extends StructuredData
{
    public ?string $name = null;

    public ?string $description = null;

    public ?string $url = null;

    public ?Brand $brand = null;

    public ?string $productGroupID = null;

    /** @var list<string> */
    public array $variesBy = [];

    /** @var list<Product> */
    public array $hasVariant = [];

    public function __construct()
    {
        $this->type = 'ProductGroup';
        $this->context = 'https://schema.org';
    }
}
