<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\LinkedData\Thing\Action;

use Setono\SyliusSEOPlugin\LinkedData\StructuredData;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\EntryPoint;
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
