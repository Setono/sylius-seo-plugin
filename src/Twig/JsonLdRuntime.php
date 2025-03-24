<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Spatie\SchemaOrg\Graph;
use Twig\Extension\RuntimeExtensionInterface;

final class JsonLdRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly Graph $graph)
    {
    }

    public function renderJsonLd(): string
    {
        return $this->graph->toScript();
    }
}
