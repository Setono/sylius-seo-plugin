<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use function Setono\SyliusSEOPlugin\sanitizeString;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductDataMapper implements ProductDataMapperInterface
{
    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        $product
            ->name((string) sanitizeString($productVariant->getName() ?? $productVariant->getProduct()?->getName(), true, 70))
            ->description((string) sanitizeString($productVariant->getProduct()?->getDescription(), true, 5000))
            ->sku((string) $productVariant->getCode())
        ;
    }
}
