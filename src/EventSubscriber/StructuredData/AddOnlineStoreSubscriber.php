<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\StructuredData;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface;
use Setono\SyliusSEOPlugin\Event\OnlineStoreAddedToGraph;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\OnlineStore;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

/**
 * Google recommends adding the OnlineStore schema to the homepage only
 * See https://developers.google.com/search/docs/appearance/structured-data/organization
 */
final class AddOnlineStoreSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Graph $graph,
        private readonly OnlineStoreDataMapperInterface $onlineStoreDataMapper,
        private readonly ChannelContextInterface $channelContext,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly string $route = 'sylius_shop_homepage',
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'populate',
        ];
    }

    public function populate(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->attributes->get('_route') !== $this->route) {
            return;
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $onlineStore = $this->graph->onlineStore();
        Assert::isInstanceOf($onlineStore, OnlineStore::class);

        $this->onlineStoreDataMapper->map($channel, $onlineStore);

        $this->eventDispatcher->dispatch(new OnlineStoreAddedToGraph($onlineStore, $channel));
    }
}
