<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

final class OpenGraphExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private readonly OpenGraph $openGraph)
    {
    }

    public function getGlobals(): array
    {
        return ['openGraph' => $this->openGraph];
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            /** @phpstan-ignore argument.type */
            new TwigFunction('setono_sylius_seo_render_open_graph', $this->renderOpenGraph(...), ['is_safe' => ['html']]),
        ];
    }

    public function renderOpenGraph(): string
    {
        return $this->openGraph->toHtml();
    }
}
