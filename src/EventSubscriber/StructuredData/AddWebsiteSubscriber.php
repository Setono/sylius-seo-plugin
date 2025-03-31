<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\StructuredData;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapperInterface;
use Setono\SyliusSEOPlugin\Event\WebsiteAddedToGraph;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\WebSite;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

final class AddWebsiteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Graph $graph,
        private readonly WebsiteDataMapperInterface $websiteDataMapper,
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

        $website = $this->graph->webSite();
        Assert::isInstanceOf($website, WebSite::class);

        $this->websiteDataMapper->map($channel, $website);

        $this->eventDispatcher->dispatch(new WebsiteAddedToGraph($website));
    }
}
