<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Website;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusSEOPlugin\StructuredData\Thing\CreativeWork\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @extends CompositeService<WebsiteDataMapperInterface>
 */
final class CompositeWebsiteDataMapper extends CompositeService implements WebsiteDataMapperInterface
{
    public function map(ChannelInterface $channel, WebSite $webSite): void
    {
        foreach ($this->services as $service) {
            $service->map($channel, $webSite);
        }
    }
}
