<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\Product;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

final readonly class ProductAddedToGraph
{
    public function __construct(
        /** This is the product that was added to the graph */
        public Product $product,

        /** If the 'schema product' above was added based on a product, this is the product */
        public ?ProductInterface $storeProduct = null,

        /** If the 'schema product' above was added based on a variant, this is the variant */
        public ?ProductVariantInterface $storeProductVariant = null,
    ) {
    }
}
