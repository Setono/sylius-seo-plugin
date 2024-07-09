<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Intangible\Offer\AggregateOffer;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class OffersProductDataMapper implements ProductDataMapperInterface
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly ProductVariantUrlGeneratorInterface $productVariantUrlGenerator,
    ) {
    }

    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $channelPricing = $productVariant->getChannelPricingForChannel($channel);
        if (null === $channelPricing) {
            return;
        }

        $product->offers = new AggregateOffer([
            new Offer(
                url: $this->productVariantUrlGenerator->generate($productVariant),
                priceCurrency: $channel->getBaseCurrency()?->getCode(),
                price: $channelPricing->getPrice(),
            ),
        ]);
    }
}
