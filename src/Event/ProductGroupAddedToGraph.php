<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\ProductGroup;

final class ProductGroupAddedToGraph
{
    public function __construct(
        public readonly ProductGroup $productGroup,
    ) {
    }
}
