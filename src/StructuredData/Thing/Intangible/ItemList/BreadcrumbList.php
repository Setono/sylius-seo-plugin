<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\ItemList;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\ListItem;

class BreadcrumbList extends StructuredData
{
    public function __construct(
        /** @var list<ListItem> $itemListElement */
        public array $itemListElement = [],
    ) {
        $this->type = 'BreadcrumbList';
        $this->context = 'https://schema.org';
    }
}
