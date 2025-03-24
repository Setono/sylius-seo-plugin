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

        $image = self::getImage($productVariant);
        if (null === $image) {
            return;
        }

        $path = $image->getPath();
        if (null === $path) {
            return;
        }

        $product->image($this->cacheManager->getBrowserPath($path, $this->filter));
    }

    private static function getImage(ProductVariantInterface $productVariant): ?ImageInterface
    {
        $image = $productVariant->getImages()->first();
        if ($image instanceof ImageInterface) {
            return $image;
        }

        $product = $productVariant->getProduct();
        if (!$product instanceof ProductInterface) {
            return null;
        }

        $image = $product->getImages()->first();
        if ($image instanceof ImageInterface) {
            return $image;
        }

        return null;
    }
}
