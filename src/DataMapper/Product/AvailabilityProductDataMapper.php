<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Enumeration\ItemAvailability;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

final class AvailabilityProductDataMapper implements ProductDataMapperInterface
{
    public function __construct(private readonly AvailabilityCheckerInterface $availabilityChecker)
    {
    }

    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        if (null !== $product->availability) {
            return;
        }

        $product->availability = $this->availabilityChecker->isStockAvailable($productVariant) ? ItemAvailability::InStock : ItemAvailability::OutOfStock;
    }
}
