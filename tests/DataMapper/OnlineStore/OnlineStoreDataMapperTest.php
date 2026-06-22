<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\OnlineStore;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\OnlineStore\OnlineStoreDataMapper;
use Spatie\SchemaOrg\ContactPoint;
use Spatie\SchemaOrg\OnlineStore;
use Spatie\SchemaOrg\PostalAddress;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OnlineStoreDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_maps_channel_data_to_online_store(): void
    {
        $shopBillingData = $this->prophesize(ShopBillingDataInterface::class);
        $shopBillingData->getTaxId()->willReturn('TAX123');
        $shopBillingData->getStreet()->willReturn('123 Main St');
        $shopBillingData->getCity()->willReturn('Copenhagen');
        $shopBillingData->getCountryCode()->willReturn('DK');
        $shopBillingData->getPostcode()->willReturn('1000');

        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn('My Store');
        $channel->getContactEmail()->willReturn('contact@example.com');
        $channel->getContactPhoneNumber()->willReturn('+45 12345678');
        $channel->getShopBillingData()->willReturn($shopBillingData->reveal());
        $channel->getHostname()->willReturn('www.example.com');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);

        $onlineStore = new OnlineStore();

        $mapper = new OnlineStoreDataMapper($urlGenerator->reveal());
        $mapper->map($channel->reveal(), $onlineStore);

        self::assertSame('My Store', $onlineStore->getProperty('name'));
        self::assertSame('TAX123', $onlineStore->getProperty('vatID'));
        self::assertSame('https://www.example.com', $onlineStore->getProperty('url'));

        $contactPoint = $onlineStore->getProperty('contactPoint');
        self::assertInstanceOf(ContactPoint::class, $contactPoint);
        self::assertSame('contact@example.com', $contactPoint->getProperty('email'));
        self::assertSame('+45 12345678', $contactPoint->getProperty('telephone'));

        $address = $onlineStore->getProperty('address');
        self::assertInstanceOf(PostalAddress::class, $address);
        self::assertSame('123 Main St', $address->getProperty('streetAddress'));
        self::assertSame('Copenhagen', $address->getProperty('addressLocality'));
        self::assertSame('DK', $address->getProperty('addressCountry'));
        self::assertSame('1000', $address->getProperty('postalCode'));
    }

    #[Test]
    public function it_uses_url_generator_when_no_hostname(): void
    {
        $shopBillingData = $this->prophesize(ShopBillingDataInterface::class);
        $shopBillingData->getTaxId()->willReturn(null);
        $shopBillingData->getStreet()->willReturn(null);
        $shopBillingData->getCity()->willReturn(null);
        $shopBillingData->getCountryCode()->willReturn(null);
        $shopBillingData->getPostcode()->willReturn(null);

        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn('My Store');
        $channel->getContactEmail()->willReturn(null);
        $channel->getContactPhoneNumber()->willReturn(null);
        $channel->getShopBillingData()->willReturn($shopBillingData->reveal());
        $channel->getHostname()->willReturn(null);

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate('sylius_shop_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://localhost/shop');

        $onlineStore = new OnlineStore();

        $mapper = new OnlineStoreDataMapper($urlGenerator->reveal());
        $mapper->map($channel->reveal(), $onlineStore);

        self::assertSame('https://localhost/shop', $onlineStore->getProperty('url'));
    }

    #[Test]
    public function it_handles_null_shop_billing_data(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn('My Store');
        $channel->getContactEmail()->willReturn('contact@example.com');
        $channel->getContactPhoneNumber()->willReturn('+45 12345678');
        $channel->getShopBillingData()->willReturn(null);
        $channel->getHostname()->willReturn('www.example.com');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);

        $onlineStore = new OnlineStore();

        $mapper = new OnlineStoreDataMapper($urlGenerator->reveal());
        $mapper->map($channel->reveal(), $onlineStore);

        // Verifies the mapper doesn't throw when shop billing data is null
        self::assertSame('My Store', $onlineStore->getProperty('name'));
        self::assertSame('https://www.example.com', $onlineStore->getProperty('url'));
    }
}
