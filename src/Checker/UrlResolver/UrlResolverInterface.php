<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;

interface UrlResolverInterface
{
    /**
     * Resolves the absolute URL that should be fetched and checked for the given page.
     *
     * @throws \RuntimeException if no resolver supports the page or the URL cannot be built
     */
    public function resolve(PageInterface $page): string;
}
