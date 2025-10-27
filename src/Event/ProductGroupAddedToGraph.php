<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductGroupAddedToGraph
{
    public function __construct(
        /** This is the product that was added to the graph */
        public readonly ProductGroup $productGroup,

        /** If the 'schema product group' above was added based on a product, this is the product */
        public readonly ?ProductInterface $storeProduct = null,
    ) {
    }
}
