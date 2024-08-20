<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use Setono\SyliusSEOPlugin\StructuredData\Thing\Product\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\String\u;

final class ProductGroupDataMapper implements ProductGroupDataMapperInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function map(ProductInterface $product, ProductGroup $productGroup): void
    {
        $description = $product->getDescription();

        $productGroup->name = $product->getName();
        // truncates at 5,000 characters because of this rather random suggestion:
        // https://support.google.com/webmasters/thread/195641191/invalid-string-length-in-field-description?hl=en
        $productGroup->description = null === $description ? null : u(strip_tags($description))->truncate(5000)->toString();
        $productGroup->productGroupID = $product->getCode();

        $productGroup->url = $this->urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => $product->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
