<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolver;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class ProductImagesResolverTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resolves_images_from_product_variant(): void
    {
        $image1 = $this->prophesize(ImageInterface::class);
        $image1->getPath()->willReturn('/path/to/image1.jpg');

        $image2 = $this->prophesize(ImageInterface::class);
        $image2->getPath()->willReturn('/path/to/image2.jpg');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([
            $image1->reveal(),
            $image2->reveal(),
        ]));

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);

        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('/path/to/image1.jpg', 'sylius_original')
            ->willReturn('https://example.com/media/cache/sylius_original/path/to/image1.jpg');
        $cacheManager->getBrowserPath('/path/to/image2.jpg', 'sylius_original')
            ->willReturn('https://example.com/media/cache/sylius_original/path/to/image2.jpg');

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
        );

        $result = $resolver->resolve($productVariant->reveal());

        self::assertSame([
            'https://example.com/media/cache/sylius_original/path/to/image1.jpg',
            'https://example.com/media/cache/sylius_original/path/to/image2.jpg',
        ], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resolves_variant_from_product_interface(): void
    {
        $image = $this->prophesize(ImageInterface::class);
        $image->getPath()->willReturn('/path/to/image.jpg');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([$image->reveal()]));

        $product = $this->prophesize(ProductInterface::class);

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);
        $productVariantResolver->getVariant($product->reveal())->willReturn($productVariant->reveal());

        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('/path/to/image.jpg', 'sylius_original')
            ->willReturn('https://example.com/media/cache/image.jpg');

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
        );

        $result = $resolver->resolve($product->reveal());

        self::assertSame(['https://example.com/media/cache/image.jpg'], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_array_when_product_has_no_variant(): void
    {
        $product = $this->prophesize(ProductInterface::class);

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);
        $productVariantResolver->getVariant($product->reveal())->willReturn(null);

        $cacheManager = $this->prophesize(CacheManager::class);

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
        );

        $result = $resolver->resolve($product->reveal());

        self::assertSame([], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_falls_back_to_product_images_when_variant_has_none(): void
    {
        $productImage = $this->prophesize(ImageInterface::class);
        $productImage->getPath()->willReturn('/path/to/product-image.jpg');

        $product = $this->prophesize(ProductInterface::class);
        $product->getImages()->willReturn(new ArrayCollection([$productImage->reveal()]));

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection());
        $productVariant->getProduct()->willReturn($product->reveal());

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);

        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('/path/to/product-image.jpg', 'sylius_original')
            ->willReturn('https://example.com/media/cache/product-image.jpg');

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
        );

        $result = $resolver->resolve($productVariant->reveal());

        self::assertSame(['https://example.com/media/cache/product-image.jpg'], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_array_when_variant_has_no_product(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection());
        $productVariant->getProduct()->willReturn(null);

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);
        $cacheManager = $this->prophesize(CacheManager::class);

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
        );

        $result = $resolver->resolve($productVariant->reveal());

        self::assertSame([], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_skips_images_with_null_path(): void
    {
        $imageWithPath = $this->prophesize(ImageInterface::class);
        $imageWithPath->getPath()->willReturn('/path/to/image.jpg');

        $imageWithNullPath = $this->prophesize(ImageInterface::class);
        $imageWithNullPath->getPath()->willReturn(null);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([
            $imageWithPath->reveal(),
            $imageWithNullPath->reveal(),
        ]));

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);

        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('/path/to/image.jpg', 'sylius_original')
            ->willReturn('https://example.com/media/cache/image.jpg');

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
        );

        $result = $resolver->resolve($productVariant->reveal());

        self::assertSame(['https://example.com/media/cache/image.jpg'], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_custom_filter(): void
    {
        $image = $this->prophesize(ImageInterface::class);
        $image->getPath()->willReturn('/path/to/image.jpg');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection([$image->reveal()]));

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);

        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getBrowserPath('/path/to/image.jpg', 'custom_filter')
            ->willReturn('https://example.com/media/cache/custom_filter/image.jpg');

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
            'custom_filter',
        );

        $result = $resolver->resolve($productVariant->reveal());

        self::assertSame(['https://example.com/media/cache/custom_filter/image.jpg'], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_array_when_variant_and_product_have_no_images(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getImages()->willReturn(new ArrayCollection());

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getImages()->willReturn(new ArrayCollection());
        $productVariant->getProduct()->willReturn($product->reveal());

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);
        $cacheManager = $this->prophesize(CacheManager::class);

        $resolver = new ProductImagesResolver(
            $productVariantResolver->reveal(),
            $cacheManager->reveal(),
        );

        $result = $resolver->resolve($productVariant->reveal());

        self::assertSame([], $result);
    }
}
