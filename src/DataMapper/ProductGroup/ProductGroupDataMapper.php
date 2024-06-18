<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use Setono\SyliusSEOPlugin\LinkedData\DTO\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductGroupDataMapper implements ProductGroupDataMapperInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function map(ProductInterface $product, ProductGroup $productGroup): void
    {
        $productGroup->name = $product->getName();
        $productGroup->description = $product->getDescription();
        $productGroup->productGroupID = $product->getCode();

        $productGroup->url = $this->urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => $product->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
