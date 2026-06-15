<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;

final readonly class OnlineStoreAddedToGraph
{
    public function __construct(
        public OnlineStore $onlineStore,
        public ChannelInterface $channel,
    ) {
    }
}
