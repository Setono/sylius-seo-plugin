<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\EventSubscriber\StructuredData;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapperInterface;
use Setono\SyliusSEOPlugin\Event\OnlineStoreAddedToGraph;
use Setono\SyliusSEOPlugin\EventSubscriber\StructuredData\AddOnlineStoreSubscriber;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\OnlineStore;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddOnlineStoreSubscriberTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_subscribes_to_kernel_request(): void
    {
        $events = AddOnlineStoreSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::REQUEST, $events);
        self::assertSame('populate', $events[KernelEvents::REQUEST]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_populates_online_store_on_homepage(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $graph = new Graph();

        $onlineStoreDataMapper = $this->prophesize(OnlineStoreDataMapperInterface::class);
        $onlineStoreDataMapper->map($channel->reveal(), Argument::type(OnlineStore::class))->shouldBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(OnlineStoreAddedToGraph::class))->shouldBeCalled();

        $subscriber = new AddOnlineStoreSubscriber(
            $graph,
            $onlineStoreDataMapper->reveal(),
            $channelContext->reveal(),
            $eventDispatcher->reveal(),
        );

        $request = new Request();
        $request->attributes->set('_route', 'sylius_shop_homepage');

        $kernel = $this->prophesize(HttpKernelInterface::class);
        $event = new RequestEvent($kernel->reveal(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->populate($event);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_populate_on_non_main_request(): void
    {
        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->shouldNotBeCalled();

        $graph = new Graph();

        $onlineStoreDataMapper = $this->prophesize(OnlineStoreDataMapperInterface::class);
        $onlineStoreDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $subscriber = new AddOnlineStoreSubscriber(
            $graph,
            $onlineStoreDataMapper->reveal(),
            $channelContext->reveal(),
            $eventDispatcher->reveal(),
        );

        $request = new Request();
        $request->attributes->set('_route', 'sylius_shop_homepage');

        $kernel = $this->prophesize(HttpKernelInterface::class);
        $event = new RequestEvent($kernel->reveal(), $request, HttpKernelInterface::SUB_REQUEST);

        $subscriber->populate($event);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_populate_on_wrong_route(): void
    {
        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->shouldNotBeCalled();

        $graph = new Graph();

        $onlineStoreDataMapper = $this->prophesize(OnlineStoreDataMapperInterface::class);
        $onlineStoreDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $subscriber = new AddOnlineStoreSubscriber(
            $graph,
            $onlineStoreDataMapper->reveal(),
            $channelContext->reveal(),
            $eventDispatcher->reveal(),
        );

        $request = new Request();
        $request->attributes->set('_route', 'sylius_shop_product_show');

        $kernel = $this->prophesize(HttpKernelInterface::class);
        $event = new RequestEvent($kernel->reveal(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->populate($event);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_custom_route(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $graph = new Graph();

        $onlineStoreDataMapper = $this->prophesize(OnlineStoreDataMapperInterface::class);
        $onlineStoreDataMapper->map($channel->reveal(), Argument::type(OnlineStore::class))->shouldBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(OnlineStoreAddedToGraph::class))->shouldBeCalled();

        $subscriber = new AddOnlineStoreSubscriber(
            $graph,
            $onlineStoreDataMapper->reveal(),
            $channelContext->reveal(),
            $eventDispatcher->reveal(),
            'custom_homepage',
        );

        $request = new Request();
        $request->attributes->set('_route', 'custom_homepage');

        $kernel = $this->prophesize(HttpKernelInterface::class);
        $event = new RequestEvent($kernel->reveal(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->populate($event);
    }
}
