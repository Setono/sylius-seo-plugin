<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Event;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Event\OnlineStoreAddedToGraph;
use Spatie\SchemaOrg\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;

final class OnlineStoreAddedToGraphTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_holds_online_store_and_channel(): void
    {
        $onlineStore = new OnlineStore();
        $channel = $this->prophesize(ChannelInterface::class);

        $event = new OnlineStoreAddedToGraph($onlineStore, $channel->reveal());

        self::assertSame($onlineStore, $event->onlineStore);
        self::assertSame($channel->reveal(), $event->channel);
    }
}
