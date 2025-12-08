<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ImageProductDataMapper implements ProductDataMapperInterface
{
    public function __construct(private readonly ProductImagesResolverInterface $productImagesResolver)
    {
    }

    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        if ($product->getProperty('image') !== null) {
            return;
        }

        $product->image($this->productImagesResolver->resolve($productVariant));
    }
}
