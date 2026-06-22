<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use function Setono\SyliusSEOPlugin\formatAmount;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGeneratorInterface;
use Spatie\SchemaOrg\ItemAvailability;
use Spatie\SchemaOrg\Product;
use Spatie\SchemaOrg\Schema;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

final readonly class OffersProductDataMapper implements ProductDataMapperInterface
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private ProductVariantUrlGeneratorInterface $productVariantUrlGenerator,
        private AvailabilityCheckerInterface $availabilityChecker,
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

        $product->offers(
            Schema::offer()
                ->url($this->productVariantUrlGenerator->generate($productVariant))
                ->priceCurrency((string) $channel->getBaseCurrency()?->getCode())
                ->price(formatAmount($channelPricing->getPrice()))
                ->availability($this->availabilityChecker->isStockAvailable($productVariant) ? ItemAvailability::InStock : ItemAvailability::OutOfStock), // @phpstan-ignore argument.type
        );
    }
}
