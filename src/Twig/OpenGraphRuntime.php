<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Twig\Extension\RuntimeExtensionInterface;

final class OpenGraphRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly OpenGraph $openGraph)
    {
    }

    public function renderOpenGraph(): string
    {
        return $this->openGraph->toHtml();
    }
}
