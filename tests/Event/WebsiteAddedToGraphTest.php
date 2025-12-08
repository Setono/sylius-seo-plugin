<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Event;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Event\WebsiteAddedToGraph;
use Spatie\SchemaOrg\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;

final class WebsiteAddedToGraphTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_holds_website_and_channel(): void
    {
        $webSite = new WebSite();
        $channel = $this->prophesize(ChannelInterface::class);

        $event = new WebsiteAddedToGraph($webSite, $channel->reveal());

        self::assertSame($webSite, $event->webSite);
        self::assertSame($channel->reveal(), $event->channel);
    }
}
