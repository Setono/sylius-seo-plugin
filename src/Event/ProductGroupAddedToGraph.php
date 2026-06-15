<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Product\Model\ProductInterface;

final readonly class ProductGroupAddedToGraph
{
    public function __construct(
        /** This is the product that was added to the graph */
        public ProductGroup $productGroup,

        /** If the 'schema product group' above was added based on a product, this is the product */
        public ?ProductInterface $storeProduct = null,
    ) {
    }
}
