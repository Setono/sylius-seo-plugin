<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Product;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Product\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class HasVariantProductGroupDataMapper implements ProductGroupDataMapperInterface
{
    public function __construct(private readonly ProductDataMapperInterface $productDataMapper)
    {
    }

    public function map(ProductInterface $product, ProductGroup $productGroup): void
    {
        /** @var ProductVariantInterface $variant */
        foreach ($product->getEnabledVariants() as $variant) {
            $p = new Product();
            $this->productDataMapper->map($variant, $p);

            $productGroup->hasVariant[] = $p;
        }
    }
}
