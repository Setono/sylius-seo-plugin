<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Controller;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Controller\RenderRobotsTxtAction;
use Setono\SyliusSEOPlugin\Model\ChannelInterface as SEOChannelInterface;
use Setono\SyliusSEOPlugin\Renderer\RobotsTxtRendererInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RenderRobotsTxtActionTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_renders_the_robots_txt_of_the_current_channel(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->willImplement(SEOChannelInterface::class);
        $channel->getRobotsTxt()->willReturn('User-agent: *');

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $renderer = $this->prophesize(RobotsTxtRendererInterface::class);
        $renderer->render('User-agent: *')->willReturn("User-agent: *\nDisallow: /admin");

        $action = new RenderRobotsTxtAction($channelContext->reveal(), $renderer->reveal());

        $response = $action();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/plain', $response->headers->get('Content-Type'));
        self::assertSame("User-agent: *\nDisallow: /admin", $response->getContent());
    }

    #[Test]
    public function it_throws_a_not_found_exception_when_the_channel_has_no_robots_txt(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->willImplement(SEOChannelInterface::class);
        $channel->getRobotsTxt()->willReturn(null);

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $renderer = $this->prophesize(RobotsTxtRendererInterface::class);
        $renderer->render(\Prophecy\Argument::any())->shouldNotBeCalled();

        $action = new RenderRobotsTxtAction($channelContext->reveal(), $renderer->reveal());

        $this->expectException(NotFoundHttpException::class);

        $action();
    }
}
