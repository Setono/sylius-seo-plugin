<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;

final class WebsiteAddedToGraph
{
    public function __construct(
        public readonly WebSite $webSite,
        public readonly ChannelInterface $channel,
    ) {
    }
}
