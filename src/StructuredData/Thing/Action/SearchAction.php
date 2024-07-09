<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Action;

use Setono\SyliusSEOPlugin\StructuredData\StructuredData;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\EntryPoint;
use Symfony\Component\Serializer\Attribute\SerializedName;

class SearchAction extends StructuredData
{
    public function __construct(
        public ?EntryPoint $target = null,
        #[SerializedName('query-input')]
        public ?string $queryInput = 'required name=query',
    ) {
        $this->type = 'SearchAction';
    }
}
