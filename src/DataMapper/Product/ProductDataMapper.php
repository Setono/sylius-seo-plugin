<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductDataMapper implements ProductDataMapperInterface
{
    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        $name = $productVariant->getProduct()?->getName();
        $product->name = null === $name ? null : strip_tags($name);

        $product->sku = $productVariant->getCode();
    }
}
