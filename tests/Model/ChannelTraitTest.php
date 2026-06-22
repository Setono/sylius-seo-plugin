<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\Model\ChannelTrait;

final class ChannelTraitTest extends TestCase
{
    #[Test]
    public function it_has_null_robots_txt_by_default(): void
    {
        $channel = new class() {
            use ChannelTrait;
        };

        self::assertNull($channel->getRobotsTxt());
    }

    #[Test]
    public function it_can_set_and_get_robots_txt(): void
    {
        $channel = new class() {
            use ChannelTrait;
        };

        $robotsTxt = "User-agent: *\nDisallow: /admin";

        $channel->setRobotsTxt($robotsTxt);

        self::assertSame($robotsTxt, $channel->getRobotsTxt());
    }

    #[Test]
    public function it_can_set_robots_txt_to_null(): void
    {
        $channel = new class() {
            use ChannelTrait;
        };

        $channel->setRobotsTxt("User-agent: *\nAllow: /");
        $channel->setRobotsTxt(null);

        self::assertNull($channel->getRobotsTxt());
    }

    #[Test]
    public function it_can_set_empty_robots_txt(): void
    {
        $channel = new class() {
            use ChannelTrait;
        };

        $channel->setRobotsTxt('');

        self::assertSame('', $channel->getRobotsTxt());
    }
}
