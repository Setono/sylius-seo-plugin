<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber;

use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface;
use Setono\SyliusSEOPlugin\LinkedData\LinkedDataContainerInterface;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Organization\OnlineBusiness\OnlineStore;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

/**
 * Google recommends to add the OnlineStore schema to the homepage.
 * See https://developers.google.com/search/docs/appearance/structured-data/organization
 */
final class HomepageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LinkedDataContainerInterface $linkedDataContainer,
        private readonly OnlineStoreDataMapperInterface $onlineStoreDataMapper,
        private readonly ChannelContextInterface $channelContext,
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

        if ($event->getRequest()->attributes->get('_route') !== 'sylius_shop_homepage') {
            return;
        }

        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $onlineStore = new OnlineStore();
        $this->onlineStoreDataMapper->map($channel, $onlineStore);

        $this->linkedDataContainer->set($onlineStore);
    }
}