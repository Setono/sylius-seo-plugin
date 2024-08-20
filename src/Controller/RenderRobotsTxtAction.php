<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Controller;

use Setono\SyliusSEOPlugin\Model\ChannelInterface;
use Setono\SyliusSEOPlugin\Renderer\RobotsTxtRendererInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

// todo add caching of the response and remember to invalidate the cache when the robots.txt changes
final class RenderRobotsTxtAction
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly RobotsTxtRendererInterface $robotsTxtRenderer,
    ) {
    }

    public function __invoke(): Response
    {
        /** @var ChannelInterface|BaseChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $robotsTxt = $channel->getRobotsTxt();
        if (null === $robotsTxt) {
            throw new NotFoundHttpException('No robots.txt configured for this channel');
        }

        return new Response(
            content: $this->robotsTxtRenderer->render($robotsTxt),
            headers: ['Content-Type' => 'text/plain'],
        );
    }
}
