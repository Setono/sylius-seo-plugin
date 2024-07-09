<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\StructuredData;

use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapperInterface;
use Setono\SyliusSEOPlugin\LinkedData\StructuredDataContainerInterface;
use Setono\SyliusSEOPlugin\LinkedData\Thing\CreativeWork\WebSite;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

final class AddWebsiteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly StructuredDataContainerInterface $linkedDataContainer,
        private readonly WebsiteDataMapperInterface $websiteDataMapper,
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

        $webSite = new WebSite();
        $this->websiteDataMapper->map($channel, $webSite);

        $this->linkedDataContainer->set($webSite);
    }
}
