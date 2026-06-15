<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\Product;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\Product\OffersProductDataMapper;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Spatie\SchemaOrg\ItemAvailability;
use Spatie\SchemaOrg\Offer;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

final class OffersProductDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_offer_with_in_stock_availability(): void
    {
        $currency = $this->prophesize(CurrencyInterface::class);
        $currency->getCode()->willReturn('EUR');

        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getBaseCurrency()->willReturn($currency->reveal());

        $channelPricing = $this->prophesize(ChannelPricingInterface::class);
        $channelPricing->getPrice()->willReturn(9999);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getChannelPricingForChannel($channel->reveal())->willReturn($channelPricing->reveal());

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $urlGenerator = $this->prophesize(ProductVariantUrlGeneratorInterface::class);
        $urlGenerator->generate($productVariant->reveal())->willReturn('https://example.com/product/variant');

        $availabilityChecker = $this->prophesize(AvailabilityCheckerInterface::class);
        $availabilityChecker->isStockAvailable($productVariant->reveal())->willReturn(true);

        $product = new Product();

        $mapper = new OffersProductDataMapper(
            $channelContext->reveal(),
            $urlGenerator->reveal(),
            $availabilityChecker->reveal(),
        );
        $mapper->map($productVariant->reveal(), $product);

        $offer = $product->getProperty('offers');
        self::assertInstanceOf(Offer::class, $offer);
        self::assertSame('https://example.com/product/variant', $offer->getProperty('url'));
        self::assertSame('EUR', $offer->getProperty('priceCurrency'));
        self::assertSame(99.99, $offer->getProperty('price'));
        self::assertSame(ItemAvailability::InStock, $offer->getProperty('availability'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_offer_with_out_of_stock_availability(): void
    {
        $currency = $this->prophesize(CurrencyInterface::class);
        $currency->getCode()->willReturn('USD');

        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getBaseCurrency()->willReturn($currency->reveal());

        $channelPricing = $this->prophesize(ChannelPricingInterface::class);
        $channelPricing->getPrice()->willReturn(5000);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getChannelPricingForChannel($channel->reveal())->willReturn($channelPricing->reveal());

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $urlGenerator = $this->prophesize(ProductVariantUrlGeneratorInterface::class);
        $urlGenerator->generate($productVariant->reveal())->willReturn('https://example.com/product');

        $availabilityChecker = $this->prophesize(AvailabilityCheckerInterface::class);
        $availabilityChecker->isStockAvailable($productVariant->reveal())->willReturn(false);

        $product = new Product();

        $mapper = new OffersProductDataMapper(
            $channelContext->reveal(),
            $urlGenerator->reveal(),
            $availabilityChecker->reveal(),
        );
        $mapper->map($productVariant->reveal(), $product);

        $offer = $product->getProperty('offers');
        self::assertInstanceOf(Offer::class, $offer);
        self::assertSame(ItemAvailability::OutOfStock, $offer->getProperty('availability'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_map_when_no_channel_pricing(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getChannelPricingForChannel($channel->reveal())->willReturn(null);

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $urlGenerator = $this->prophesize(ProductVariantUrlGeneratorInterface::class);
        $availabilityChecker = $this->prophesize(AvailabilityCheckerInterface::class);

        $product = new Product();

        $mapper = new OffersProductDataMapper(
            $channelContext->reveal(),
            $urlGenerator->reveal(),
            $availabilityChecker->reveal(),
        );
        $mapper->map($productVariant->reveal(), $product);

        self::assertNull($product->getProperty('offers'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_null_price(): void
    {
        $currency = $this->prophesize(CurrencyInterface::class);
        $currency->getCode()->willReturn('EUR');

        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getBaseCurrency()->willReturn($currency->reveal());

        $channelPricing = $this->prophesize(ChannelPricingInterface::class);
        $channelPricing->getPrice()->willReturn(null);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getChannelPricingForChannel($channel->reveal())->willReturn($channelPricing->reveal());

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $urlGenerator = $this->prophesize(ProductVariantUrlGeneratorInterface::class);
        $urlGenerator->generate($productVariant->reveal())->willReturn('https://example.com/product');

        $availabilityChecker = $this->prophesize(AvailabilityCheckerInterface::class);
        $availabilityChecker->isStockAvailable($productVariant->reveal())->willReturn(true);

        $product = new Product();

        $mapper = new OffersProductDataMapper(
            $channelContext->reveal(),
            $urlGenerator->reveal(),
            $availabilityChecker->reveal(),
        );
        $mapper->map($productVariant->reveal(), $product);

        $offer = $product->getProperty('offers');
        self::assertInstanceOf(Offer::class, $offer);
        self::assertSame(0.0, $offer->getProperty('price'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_null_currency(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getBaseCurrency()->willReturn(null);

        $channelPricing = $this->prophesize(ChannelPricingInterface::class);
        $channelPricing->getPrice()->willReturn(1000);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getChannelPricingForChannel($channel->reveal())->willReturn($channelPricing->reveal());

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel->reveal());

        $urlGenerator = $this->prophesize(ProductVariantUrlGeneratorInterface::class);
        $urlGenerator->generate($productVariant->reveal())->willReturn('https://example.com/product');

        $availabilityChecker = $this->prophesize(AvailabilityCheckerInterface::class);
        $availabilityChecker->isStockAvailable($productVariant->reveal())->willReturn(true);

        $product = new Product();

        $mapper = new OffersProductDataMapper(
            $channelContext->reveal(),
            $urlGenerator->reveal(),
            $availabilityChecker->reveal(),
        );
        $mapper->map($productVariant->reveal(), $product);

        // Verifies the mapper doesn't throw when currency is null
        $offer = $product->getProperty('offers');
        self::assertInstanceOf(Offer::class, $offer);
        self::assertSame(10.0, $offer->getProperty('price'));
    }
}
