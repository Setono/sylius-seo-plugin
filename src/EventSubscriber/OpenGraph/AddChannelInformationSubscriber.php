<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class AddChannelInformationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private OpenGraph $openGraph,
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
        $this->openGraph->locale($this->localeContext->getLocaleCode());
    }
}
