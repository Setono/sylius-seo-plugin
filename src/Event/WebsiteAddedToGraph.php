<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;

final readonly class WebsiteAddedToGraph
{
    public function __construct(
        public WebSite $webSite,
        public ChannelInterface $channel,
    ) {
    }
}
