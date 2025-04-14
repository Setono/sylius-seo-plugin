<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\Product;

final class ProductAddedToGraph
{
    public function __construct(
        public readonly Product $product,
    ) {
    }
}
