<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;

abstract class AbstractPageUrlResolver implements PageUrlResolverInterface
{
    public function __construct(protected readonly ChannelUrlGeneratorInterface $urlGenerator)
    {
    }

    public function supports(PageInterface $page): bool
    {
        return $page->getType() === $this->getType();
    }
}
