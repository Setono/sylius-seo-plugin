<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use Setono\SyliusSEOPlugin\DataMapper\AbstractDataMapper;
use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @extends AbstractDataMapper<ProductGroupDataMapperInterface>
 */
final class CompositeProductGroupDataMapper extends AbstractDataMapper implements ProductGroupDataMapperInterface
{
    public function map(ProductInterface $product, ProductGroup $productGroup): void
    {
        try {
            foreach ($this->services as $service) {
                $service->map($product, $productGroup);
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'There was an error mapping the object (%s): %s',
                $product::class,
                $e->getMessage(),
            ), ['exception' => $e]);
        }
    }
}
