<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;

/**
 * Delegates URL resolution to the first registered page-type resolver that supports the page.
 * Also exposes the list of available page types for the admin form.
 */
final class CompositeUrlResolver implements UrlResolverInterface
{
    /**
     * @param iterable<PageUrlResolverInterface> $resolvers
     */
    public function __construct(private readonly iterable $resolvers)
    {
    }

    public function resolve(PageInterface $page): string
    {
        foreach ($this->resolvers as $resolver) {
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
        foreach ($this->resolvers as $resolver) {
            $types[] = $resolver->getType();
        }

        return $types;
    }
}
