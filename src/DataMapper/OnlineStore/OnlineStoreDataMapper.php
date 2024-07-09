<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\OnlineStore;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\StructuredValue\ContactPoint;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Organization\OnlineBusiness\OnlineStore;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OnlineStoreDataMapper implements OnlineStoreDataMapperInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function map(ChannelInterface $channel, OnlineStore $onlineStore): void
    {
        $onlineStore->name = $channel->getName();
        $onlineStore->vatID = $channel->getShopBillingData()?->getTaxId();
        $onlineStore->contactPoint = new ContactPoint(
            email: $channel->getContactEmail(),
            telephone: $channel->getContactPhoneNumber(),
        );
        $onlineStore->address = new ContactPoint\PostalAddress(
            streetAddress: $channel->getShopBillingData()?->getStreet(),
            addressLocality: $channel->getShopBillingData()?->getCity(),
            addressCountry: $channel->getShopBillingData()?->getCountryCode(),
            postalCode: $channel->getShopBillingData()?->getPostcode(),
        );

        $hostname = $channel->getHostname();
        if (null === $hostname) {
            $onlineStore->url = $this->urlGenerator->generate(name: 'sylius_shop_homepage', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $onlineStore->url = sprintf('https://%s', $hostname);
        }
    }
}
