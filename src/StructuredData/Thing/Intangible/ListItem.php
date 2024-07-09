<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class ListItem extends StructuredData
{
    public function __construct(
        public int $position,
        public string $name,
        /** When the ListItem is part of a BreadcrumbList, this is the URL of the breadcrumb */
        public string $item,
    ) {
        $this->type = 'ListItem';
    }
}
