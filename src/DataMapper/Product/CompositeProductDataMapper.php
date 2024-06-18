<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusSEOPlugin\LinkedData\DTO\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @extends CompositeService<ProductDataMapperInterface>
 */
final class CompositeProductDataMapper extends CompositeService implements ProductDataMapperInterface
{
    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        foreach ($this->services as $service) {
            $service->map($productVariant, $product);
        }
    }
}
