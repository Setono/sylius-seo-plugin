<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\UrlResolver;

use Setono\SyliusSEOPlugin\Model\PageInterface;

/**
 * A page-type strategy. Implement and tag with `setono_sylius_seo.page_url_resolver` (or rely on
 * autoconfiguration) to add a new page type that operators can select when defining a page.
 */
interface PageUrlResolverInterface extends UrlResolverInterface
{
    /**
     * The page type code this resolver handles (e.g. "homepage", "product"). Shown in the admin.
     */
    public function getType(): string;

    public function supports(PageInterface $page): bool;
}
