<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\DataMapper\Product;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ImageProductDataMapper implements ProductDataMapperInterface
{
    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly string $filter = 'sylius_shop_product_large_thumbnail',
    ) {
    }

    public function map(ProductVariantInterface $productVariant, Product $product): void
    {
        if ($product->getProperty('image') !== null) {
            return;
        }

        $images = self::getImages($productVariant);
        if ([] === $images) {
            return;
        }

        $imageUrls = [];
        foreach ($images as $image) {
            $path = $image->getPath();
            if (null !== $path) {
                $imageUrls[] = $this->cacheManager->getBrowserPath($path, $this->filter);
            }
        }

        if ([] === $imageUrls) {
            return;
        }

        $product->image($imageUrls);
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
