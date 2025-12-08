<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Spatie\SchemaOrg\Graph;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class JsonLdExtension extends AbstractExtension
{
    public function __construct(private readonly Graph $graph)
    {
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_seo_render_json_ld', $this->renderJsonLd(...), ['is_safe' => ['html']]),
        ];
    }

    public function renderJsonLd(): string
    {
        return $this->graph->toScript();
    }
}
