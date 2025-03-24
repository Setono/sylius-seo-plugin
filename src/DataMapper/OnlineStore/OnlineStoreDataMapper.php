<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\OnlineStore;

use Spatie\SchemaOrg\OnlineStore;
use Spatie\SchemaOrg\Schema;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OnlineStoreDataMapper implements OnlineStoreDataMapperInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function map(ChannelInterface $channel, OnlineStore $onlineStore): void
    {
        $onlineStore
            ->name((string) $channel->getName())
            ->vatID((string) $channel->getShopBillingData()?->getTaxId())
            ->contactPoint(
                Schema::contactPoint()
                    ->email((string) $channel->getContactEmail())
                    ->telephone((string) $channel->getContactPhoneNumber()),
            )
            ->address(
                Schema::postalAddress()
                    ->streetAddress((string) $channel->getShopBillingData()?->getStreet())
                    ->addressLocality((string) $channel->getShopBillingData()?->getCity())
                    ->addressCountry((string) $channel->getShopBillingData()?->getCountryCode())
                    ->postalCode((string) $channel->getShopBillingData()?->getPostcode()),
            )
        ;

        $hostname = $channel->getHostname();
        if (null === $hostname) {
            $onlineStore->url($this->urlGenerator->generate(name: 'sylius_shop_homepage', referenceType: UrlGeneratorInterface::ABSOLUTE_URL));
        } else {
            $onlineStore->url(sprintf('https://%s', $hostname));
        }
    }
}
