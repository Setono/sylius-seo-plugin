<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Resolver;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class ProductImagesResolver implements ProductImagesResolverInterface
{
    public function __construct(
        private readonly ProductVariantResolverInterface $productVariantResolver,
        private readonly CacheManager $cacheManager,
        private readonly string $filter = 'sylius_original',
    ) {
    }

    public function resolve(ProductInterface|ProductVariantInterface $product): array
    {
        if ($product instanceof ProductInterface) {
            $product = $this->productVariantResolver->getVariant($product);
            if (!$product instanceof ProductVariantInterface) {
                return [];
            }
        }

        $images = [];

        foreach (self::getImages($product) as $image) {
            $path = $image->getPath();
            if (null === $path) {
                continue;
            }

            $images[] = $this->cacheManager->getBrowserPath($path, $this->filter);
        }

        return $images;
    }

    /**
     * @return list<ImageInterface>
     */
    private static function getImages(ProductVariantInterface $productVariant): array
    {
        $images = [];

        // First, try to get images from the product variant
        foreach ($productVariant->getImages() as $image) {
            if ($image instanceof ImageInterface) {
                $images[] = $image;
            }
        }

        // If variant has images, use those
        if ([] !== $images) {
            return $images;
        }

        // Otherwise, fall back to product images
        $product = $productVariant->getProduct();
        if (!$product instanceof ProductInterface) {
            return [];
        }

        foreach ($product->getImages() as $image) {
            if ($image instanceof ImageInterface) {
                $images[] = $image;
            }
        }

        return $images;
    }
}
