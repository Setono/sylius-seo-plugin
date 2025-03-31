<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Event;

use Spatie\SchemaOrg\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;

final class OnlineStoreAddedToGraph
{
    public function __construct(
        public readonly OnlineStore $onlineStore,
        public readonly ChannelInterface $channel,
    ) {
    }
}
