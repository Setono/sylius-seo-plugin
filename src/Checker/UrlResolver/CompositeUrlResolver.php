<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusSEOPlugin\Model\PageInterface;

/**
 * Delegates URL resolution to the first registered page-type resolver that supports the page.
 * Also exposes the list of available page types for the admin form.
 *
 * The resolvers are collected by the composite compiler pass registered in the plugin bundle.
 *
 * @extends CompositeService<PageUrlResolverInterface>
 */
final class CompositeUrlResolver extends CompositeService implements UrlResolverInterface
{
    public function resolve(PageInterface $page): string
    {
        foreach ($this->services as $resolver) {
            if ($resolver->supports($page)) {
                return $resolver->resolve($page);
            }
        }

        throw new \RuntimeException(sprintf('No URL resolver supports the page type "%s".', (string) $page->getType()));
    }

    /**
     * @return list<string>
     */
    public function getTypes(): array
    {
        $types = [];
        foreach ($this->services as $resolver) {
            $types[] = $resolver->getType();
        }

        return $types;
    }
}
