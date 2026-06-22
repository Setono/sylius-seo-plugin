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
use Symfony\Component\Routing\RouterInterface;

final class ChannelUrlGeneratorTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_builds_an_absolute_url_from_the_channel_hostname_and_scheme(): void
    {
        $router = $this->prophesize(RouterInterface::class);
        $router->generate('sylius_shop_homepage', ['_locale' => 'en_US'], UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/en_US');

        $generator = new ChannelUrlGenerator($router->reveal(), 'https', null);

        self::assertSame('https://example.com/en_US', $generator->generate($this->page('example.com', 'en_US'), 'sylius_shop_homepage'));
    }

    #[Test]
    public function it_prefers_the_configured_base_url_over_the_channel_hostname(): void
    {
        $router = $this->prophesize(RouterInterface::class);
        $router->generate('sylius_shop_homepage', Argument::any(), UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/en_US');

        $generator = new ChannelUrlGenerator($router->reveal(), 'https', 'http://127.0.0.1:8080');

        self::assertSame('http://127.0.0.1:8080/en_US', $generator->generate($this->page('example.com', 'en_US'), 'sylius_shop_homepage'));
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
