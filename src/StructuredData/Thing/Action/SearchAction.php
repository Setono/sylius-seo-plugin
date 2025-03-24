<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\StructuredData\Thing\Action;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Action;
use Symfony\Component\Serializer\Attribute\SerializedName;

class SearchAction extends Action
{
    #[SerializedName('query-input')]
    public ?string $queryInput = 'required name=query';
}
