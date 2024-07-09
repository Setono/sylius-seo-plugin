<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Website;

use Setono\SyliusSEOPlugin\LinkedData\Thing\Action\SearchAction;
use Setono\SyliusSEOPlugin\LinkedData\Thing\CreativeWork\WebSite;
use Setono\SyliusSEOPlugin\LinkedData\Thing\Intangible\EntryPoint;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class WebsiteDataMapper implements WebsiteDataMapperInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        /** @var array{ route: string, query_parameter: string } $searchUrlTemplate */
        private readonly array $searchUrlTemplate,
    ) {
    }

    public function map(ChannelInterface $channel, WebSite $webSite): void
    {
        $hostname = $channel->getHostname();
        if (null === $hostname) {
            $webSite->url = $this->urlGenerator->generate(
                name: 'sylius_shop_homepage',
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
            );
        } else {
            $webSite->url = sprintf('https://%s', $hostname);
        }

        $webSite->potentialAction = new SearchAction(
            target: new EntryPoint(
                rawurldecode($this->urlGenerator->generate(
                    $this->searchUrlTemplate['route'],
                    [$this->searchUrlTemplate['query_parameter'] => '{query}'],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                )),
            ),
        );
    }
}
