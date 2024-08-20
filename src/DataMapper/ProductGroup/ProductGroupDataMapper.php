<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\ProductGroup;

use function Setono\SyliusSEOPlugin\sanitizeString;
use Setono\SyliusSEOPlugin\StructuredData\Thing\Product\ProductGroup;
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
        // truncates at 5,000 characters because of this rather random suggestion:
        // https://support.google.com/webmasters/thread/195641191/invalid-string-length-in-field-description?hl=en
        $productGroup->description = sanitizeString($product->getDescription(), true, 5000);
        $productGroup->productGroupID = $product->getCode();

        $productGroup->url = $this->urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => $product->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
