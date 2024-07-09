<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductDataMapperInterface
{
    /**
     * Maps a product variant to a product
     */
    public function map(ProductVariantInterface $productVariant, Product $product): void;
}
