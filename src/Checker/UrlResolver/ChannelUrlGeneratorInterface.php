<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;

interface ChannelUrlGeneratorInterface
{
    /**
     * Builds the absolute URL to fetch for the given page and route.
     *
     * @param array<string, mixed> $parameters
     */
    public function generate(PageInterface $page, string $route, array $parameters = []): string;

    /**
     * The locale to resolve the page in: the page's own locale, else the channel's default locale.
     */
    public function localeCode(PageInterface $page): ?string;
}
