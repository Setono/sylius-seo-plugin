<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductDataMapper implements ProductDataMapperInterface
{
    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        $product
            ->name(trim(strip_tags((string) ($productVariant->getName() ?? $productVariant->getProduct()?->getName()))))
            ->sku((string) $productVariant->getCode())
        ;
    }
}
