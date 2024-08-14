<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\SyliusSEOPlugin\DataMapper\AbstractDataMapper;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @extends AbstractDataMapper<ProductDataMapperInterface>
 */
final class CompositeProductDataMapper extends AbstractDataMapper implements ProductDataMapperInterface
{
    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        try {
            foreach ($this->services as $service) {
                $service->map($productVariant, $product);
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'There was an error mapping the object (%s): %s',
                $productVariant::class,
                $e->getMessage(),
            ), ['exception' => $e]);
        }
    }
}
