<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Checker\UrlResolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Checker\UrlResolver\ChannelUrlGenerator;
use Setono\SyliusSEOPlugin\Model\Page;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

final class ChannelUrlGeneratorTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_overrides_the_request_context_host_with_the_channel_hostname(): void
    {
        $context = new RequestContext('', 'GET', 'localhost', 'http');

        $router = $this->prophesize(RouterInterface::class);
        $router->getContext()->willReturn($context);
        // The generator sets the channel hostname on the context before generating, so the router
        // produces an absolute URL on the channel's own domain.
        $router->generate('sylius_shop_homepage', ['_locale' => 'en_US'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->will(fn (): string => $context->getScheme() . '://' . $context->getHost() . '/en_US')
        ;

        $generator = new ChannelUrlGenerator($router->reveal());

        self::assertSame('http://example.com/en_US', $generator->generate($this->page('example.com', 'en_US'), 'sylius_shop_homepage'));
    }

    #[Test]
    public function it_restores_the_original_request_context_host_after_generating(): void
    {
        $context = new RequestContext('', 'GET', 'localhost', 'http');

        $router = $this->prophesize(RouterInterface::class);
        $router->getContext()->willReturn($context);
        $router->generate('sylius_shop_homepage', Argument::any(), UrlGeneratorInterface::ABSOLUTE_URL)->willReturn('http://example.com/en_US');

        $generator = new ChannelUrlGenerator($router->reveal());
        $generator->generate($this->page('example.com', 'en_US'), 'sylius_shop_homepage');

        self::assertSame('localhost', $context->getHost());
    }

    #[Test]
    public function it_uses_the_page_locale_when_set(): void
    {
        $page = $this->page('example.com', 'en_US');
        $page->setLocaleCode('da_DK');

        self::assertSame('da_DK', (new ChannelUrlGenerator($this->prophesize(RouterInterface::class)->reveal()))->localeCode($page));
    }

    private function page(string $hostname, string $defaultLocale): Page
    {
        $locale = $this->prophesize(LocaleInterface::class);
        $locale->getCode()->willReturn($defaultLocale);

        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getHostname()->willReturn($hostname);
        $channel->getDefaultLocale()->willReturn($locale->reveal());

        $page = new Page();
        $page->setChannel($channel->reveal());

        return $page;
    }
}
