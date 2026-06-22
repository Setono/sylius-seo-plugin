<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;

final class HomepagePageUrlResolver extends AbstractPageUrlResolver
{
    public function getType(): string
    {
        return 'homepage';
    }

    public function resolve(PageInterface $page): string
    {
        return $this->urlGenerator->generate($page, 'sylius_shop_homepage');
    }
}
