<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;

class EntryPoint extends StructuredData
{
    public function __construct(
        /**
         * Remember to match the placeholder name with the one given in SearchAction::$queryInput. The default placeholder name is 'query'.
         */
        public ?string $urlTemplate = null,
    ) {
        $this->type = 'EntryPoint';
    }
}
