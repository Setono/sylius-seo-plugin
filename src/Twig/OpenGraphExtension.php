<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OpenGraphExtension extends AbstractExtension
{
    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            /** @phpstan-ignore argument.type */
            new TwigFunction('setono_sylius_seo_render_open_graph', [OpenGraphRuntime::class, 'renderOpenGraph'], ['is_safe' => ['html']]),
        ];
    }
}
