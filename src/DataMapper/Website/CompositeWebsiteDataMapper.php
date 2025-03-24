<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Website;

use Setono\SyliusSEOPlugin\DataMapper\AbstractDataMapper;
use Spatie\SchemaOrg\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @extends AbstractDataMapper<WebsiteDataMapperInterface>
 */
final class CompositeWebsiteDataMapper extends AbstractDataMapper implements WebsiteDataMapperInterface
{
    public function map(ChannelInterface $channel, WebSite $webSite): void
    {
        try {
            foreach ($this->services as $service) {
                $service->map($channel, $webSite);
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
