<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Spatie\SchemaOrg\ProductGroup;
use Spatie\SchemaOrg\Schema;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class HasVariantProductGroupDataMapper implements ProductGroupDataMapperInterface
{
    public function __construct(private readonly ProductDataMapperInterface $productDataMapper)
    {
    }

    public function map(ProductInterface $product, ProductGroup $productGroup): void
    {
        $hasVariant = [];

        /** @var ProductVariantInterface $variant */
        foreach ($product->getEnabledVariants() as $variant) {
            $p = Schema::product();
            $this->productDataMapper->map($variant, $p);

            $hasVariant[] = $p;
        }

        $productGroup->hasVariant($hasVariant);
    }
}
