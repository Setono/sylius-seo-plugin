<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Product\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @extends CompositeService<ProductGroupDataMapperInterface>
 */
final class CompositeProductGroupDataMapper extends CompositeService implements ProductGroupDataMapperInterface
{
    public function map(ProductInterface $product, ProductGroup $productGroup): void
    {
        foreach ($this->services as $service) {
            $service->map($product, $productGroup);
        }
    }
}
