<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddChannelInformationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly OpenGraph $openGraph,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'add',
        ];
    }

    public function add(): void
    {
        $channel = $this->channelContext->getChannel();

        $this->openGraph->siteName((string) $channel->getName());
    }
}
