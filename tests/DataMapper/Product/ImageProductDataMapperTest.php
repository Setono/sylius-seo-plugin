<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\Product\ImageProductDataMapper;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ImageProductDataMapperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_maps_all_variant_images(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('path1.jpg', 'sylius_shop_product_large_thumbnail')
            ->willReturn('https://example.com/media/cache/path1.jpg');
        $cacheManager->getBrowserPath('path2.jpg', 'sylius_shop_product_large_thumbnail')
            ->willReturn('https://example.com/media/cache/path2.jpg');
        $cacheManager->getBrowserPath('path3.jpg', 'sylius_shop_product_large_thumbnail')
            ->willReturn('https://example.com/media/cache/path3.jpg');

        $image1 = $this->prophesize(ImageInterface::class);
        $image1->getPath()->willReturn('path1.jpg');

        $image2 = $this->prophesize(ImageInterface::class);
        $image2->getPath()->willReturn('path2.jpg');

        $image3 = $this->prophesize(ImageInterface::class);
        $image3->getPath()->willReturn('path3.jpg');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([
            $image1->reveal(),
            $image2->reveal(),
            $image3->reveal(),
        ]));

        $product = new Product();

        $mapper = new ImageProductDataMapper($cacheManager->reveal());
        $mapper->map($productVariant->reveal(), $product);

        $this->assertSame([
            'https://example.com/media/cache/path1.jpg',
            'https://example.com/media/cache/path2.jpg',
            'https://example.com/media/cache/path3.jpg',
        ], $product->getProperty('image'));
    }

    /**
     * @test
     */
    public function it_falls_back_to_product_images_when_variant_has_no_images(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('product-path1.jpg', 'sylius_shop_product_large_thumbnail')
            ->willReturn('https://example.com/media/cache/product-path1.jpg');
        $cacheManager->getBrowserPath('product-path2.jpg', 'sylius_shop_product_large_thumbnail')
            ->willReturn('https://example.com/media/cache/product-path2.jpg');

        $image1 = $this->prophesize(ImageInterface::class);
        $image1->getPath()->willReturn('product-path1.jpg');

        $image2 = $this->prophesize(ImageInterface::class);
        $image2->getPath()->willReturn('product-path2.jpg');

        $syliusProduct = $this->prophesize(ProductInterface::class);
        $syliusProduct->getImages()->willReturn(new ArrayCollection([
            $image1->reveal(),
            $image2->reveal(),
        ]));

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([]));
        $productVariant->getProduct()->willReturn($syliusProduct->reveal());

        $product = new Product();

        $mapper = new ImageProductDataMapper($cacheManager->reveal());
        $mapper->map($productVariant->reveal(), $product);

        $this->assertSame([
            'https://example.com/media/cache/product-path1.jpg',
            'https://example.com/media/cache/product-path2.jpg',
        ], $product->getProperty('image'));
    }

    /**
     * @test
     */
    public function it_skips_images_with_null_path(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('path1.jpg', 'sylius_shop_product_large_thumbnail')
            ->willReturn('https://example.com/media/cache/path1.jpg');
        $cacheManager->getBrowserPath('path3.jpg', 'sylius_shop_product_large_thumbnail')
            ->willReturn('https://example.com/media/cache/path3.jpg');

        $image1 = $this->prophesize(ImageInterface::class);
        $image1->getPath()->willReturn('path1.jpg');

        $image2 = $this->prophesize(ImageInterface::class);
        $image2->getPath()->willReturn(null);

        $image3 = $this->prophesize(ImageInterface::class);
        $image3->getPath()->willReturn('path3.jpg');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([
            $image1->reveal(),
            $image2->reveal(),
            $image3->reveal(),
        ]));

        $product = new Product();

        $mapper = new ImageProductDataMapper($cacheManager->reveal());
        $mapper->map($productVariant->reveal(), $product);

        $this->assertSame([
            'https://example.com/media/cache/path1.jpg',
            'https://example.com/media/cache/path3.jpg',
        ], $product->getProperty('image'));
    }

    /**
     * @test
     */
    public function it_does_not_map_when_image_property_already_set(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);

        $productVariant = $this->prophesize(ProductVariantInterface::class);

        $product = new Product();
        $product->image('https://example.com/existing-image.jpg');

        $mapper = new ImageProductDataMapper($cacheManager->reveal());
        $mapper->map($productVariant->reveal(), $product);

        $this->assertSame('https://example.com/existing-image.jpg', $product->getProperty('image'));
    }

    /**
     * @test
     */
    public function it_does_not_map_when_no_images_available(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);

        $syliusProduct = $this->prophesize(ProductInterface::class);
        $syliusProduct->getImages()->willReturn(new ArrayCollection([]));

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([]));
        $productVariant->getProduct()->willReturn($syliusProduct->reveal());

        $product = new Product();

        $mapper = new ImageProductDataMapper($cacheManager->reveal());
        $mapper->map($productVariant->reveal(), $product);

        $this->assertNull($product->getProperty('image'));
    }

    /**
     * @test
     */
    public function it_does_not_map_when_variant_has_no_product(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([]));
        $productVariant->getProduct()->willReturn(null);

        $product = new Product();

        $mapper = new ImageProductDataMapper($cacheManager->reveal());
        $mapper->map($productVariant->reveal(), $product);

        $this->assertNull($product->getProperty('image'));
    }

    /**
     * @test
     */
    public function it_uses_custom_filter(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('path1.jpg', 'custom_filter')
            ->willReturn('https://example.com/media/cache/custom/path1.jpg');

        $image1 = $this->prophesize(ImageInterface::class);
        $image1->getPath()->willReturn('path1.jpg');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([
            $image1->reveal(),
        ]));

        $product = new Product();

        $mapper = new ImageProductDataMapper($cacheManager->reveal(), 'custom_filter');
        $mapper->map($productVariant->reveal(), $product);

        $this->assertSame([
            'https://example.com/media/cache/custom/path1.jpg',
        ], $product->getProperty('image'));
    }
}
