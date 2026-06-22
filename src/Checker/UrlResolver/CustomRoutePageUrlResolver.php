<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;

/**
 * Resolves an arbitrary Symfony route configured on the page, with the page's static route
 * parameters. Lets operators test any route the plugin does not provide a dedicated type for.
 */
final class CustomRoutePageUrlResolver extends AbstractPageUrlResolver
{
    public function getType(): string
    {
        return 'custom_route';
    }

    public function resolve(PageInterface $page): string
    {
        $route = $page->getRouteName();
        if (null === $route || '' === $route) {
            throw new \RuntimeException(sprintf('Page "%s" of type "custom_route" has no route name configured.', (string) $page->getName()));
        }

        return $this->urlGenerator->generate($page, $route, $page->getRouteParameters());
    }
}
