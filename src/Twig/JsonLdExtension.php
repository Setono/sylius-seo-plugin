<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class JsonLdExtension extends AbstractExtension
{
    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        /** @psalm-suppress InvalidArgument */
        return [
            new TwigFunction('setono_sylius_seo_render_json_ld', [JsonLdRuntime::class, 'renderJsonLd'], ['is_safe' => ['html']]),
        ];
    }
}
