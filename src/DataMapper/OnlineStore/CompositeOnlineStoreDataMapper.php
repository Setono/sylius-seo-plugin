<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\OnlineStore;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Organization\OnlineBusiness\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @extends CompositeService<OnlineStoreDataMapperInterface>
 */
final class CompositeOnlineStoreDataMapper extends CompositeService implements OnlineStoreDataMapperInterface
{
    public function map(ChannelInterface $channel, OnlineStore $onlineStore): void
    {
        foreach ($this->services as $service) {
            $service->map($channel, $onlineStore);
        }
    }
}
