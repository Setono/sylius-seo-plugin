<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\OnlineStore;

use Setono\SyliusSEOPlugin\DataMapper\AbstractDataMapper;
use Spatie\SchemaOrg\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @extends AbstractDataMapper<OnlineStoreDataMapperInterface>
 */
final class CompositeOnlineStoreDataMapper extends AbstractDataMapper implements OnlineStoreDataMapperInterface
{
    public function map(ChannelInterface $channel, OnlineStore $onlineStore): void
    {
        try {
            foreach ($this->services as $service) {
                $service->map($channel, $onlineStore);
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'There was an error mapping the object (%s): %s',
                $channel::class,
                $e->getMessage(),
            ), ['exception' => $e]);
        }
    }
}
