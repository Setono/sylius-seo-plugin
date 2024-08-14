<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductDataMapper implements ProductDataMapperInterface
{
    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        $name = self::getName($productVariant);
        $product->name = null === $name ? null : trim(strip_tags($name));

        $product->sku = $productVariant->getCode();
    }

    private static function getName(ProductVariantInterface $productVariant): ?string
    {
        $name = $productVariant->getName();
        if (null !== $name && '' !== $name) {
            return $name;
        }

        return $productVariant->getProduct()?->getName();
    }
}
