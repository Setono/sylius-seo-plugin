<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Resolver;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductImagesResolverInterface
{
    /**
     * Will return a list of images for the given product variant. The first image is the primary image
     *
     * @return list<string>
     */
    public function resolve(ProductInterface|ProductVariantInterface $product): array;
}
