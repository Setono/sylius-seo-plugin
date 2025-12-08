<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\EventSubscriber\OpenGraph;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph\AddChannelInformationSubscriber;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddChannelInformationSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_subscribes_to_kernel_request(): void
    {
        $events = AddChannelInformationSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::REQUEST, $events);
        self::assertSame('add', $events[KernelEvents::REQUEST]);
    }

    /**
     * @test
     */
    public function it_adds_channel_information_to_open_graph(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn('My Store');

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $localeContext = $this->prophesize(LocaleContextInterface::class);
        $localeContext->getLocaleCode()->willReturn('en_US');

        $openGraph = new OpenGraph();

        $subscriber = new AddChannelInformationSubscriber(
            $channelContext->reveal(),
            $localeContext->reveal(),
            $openGraph,
        );

        $subscriber->add();

        self::assertSame('My Store', $openGraph->getSiteName());
        self::assertSame('en_US', $openGraph->getLocale());
    }
}
