<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;

interface ProductGroupDataMapperInterface
{
    /**
     * Maps a product to a product group
     */
    public function map(ProductInterface $product, ProductGroup $productGroup): void;
}
