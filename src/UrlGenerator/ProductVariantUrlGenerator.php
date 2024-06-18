<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\UrlGenerator;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductVariantUrlGenerator implements ProductVariantUrlGeneratorInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function generate(ProductVariantInterface $productVariant): string
    {
        return $this->urlGenerator->generate(
            name: 'sylius_shop_product_show',
            parameters: ['slug' => $productVariant->getProduct()?->getSlug()],
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
