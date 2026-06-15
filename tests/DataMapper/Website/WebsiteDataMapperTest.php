<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\Website;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\Website\WebsiteDataMapper;
use Spatie\SchemaOrg\EntryPoint;
use Spatie\SchemaOrg\SearchAction;
use Spatie\SchemaOrg\WebSite;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class WebsiteDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_maps_channel_with_hostname_to_website(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getHostname()->willReturn('www.example.com');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_search',
            ['query' => '{query}'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://www.example.com/search?query=%7Bquery%7D');

        $webSite = new WebSite();

        $mapper = new WebsiteDataMapper($urlGenerator->reveal(), [
            'route' => 'sylius_shop_search',
            'query_parameter' => 'query',
        ]);
        $mapper->map($channel->reveal(), $webSite);

        self::assertSame('https://www.example.com', $webSite->getProperty('url'));
    }

    #[Test]
    public function it_uses_url_generator_when_no_hostname(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getHostname()->willReturn(null);

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_homepage',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://localhost/shop');

        $urlGenerator->generate(
            'sylius_shop_search',
            ['query' => '{query}'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://localhost/shop/search?query=%7Bquery%7D');

        $webSite = new WebSite();

        $mapper = new WebsiteDataMapper($urlGenerator->reveal(), [
            'route' => 'sylius_shop_search',
            'query_parameter' => 'query',
        ]);
        $mapper->map($channel->reveal(), $webSite);

        self::assertSame('https://localhost/shop', $webSite->getProperty('url'));
    }

    #[Test]
    public function it_sets_search_action_with_url_template(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getHostname()->willReturn('www.example.com');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_search',
            ['q' => '{query}'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://www.example.com/search?q=%7Bquery%7D');

        $webSite = new WebSite();

        $mapper = new WebsiteDataMapper($urlGenerator->reveal(), [
            'route' => 'sylius_shop_search',
            'query_parameter' => 'q',
        ]);
        $mapper->map($channel->reveal(), $webSite);

        $potentialAction = $webSite->getProperty('potentialAction');
        self::assertInstanceOf(SearchAction::class, $potentialAction);

        $target = $potentialAction->getProperty('target');
        self::assertInstanceOf(EntryPoint::class, $target);

        $urlTemplate = $target->getProperty('urlTemplate');
        self::assertIsString($urlTemplate);
        self::assertStringContainsString('{query}', $urlTemplate);
    }
}
