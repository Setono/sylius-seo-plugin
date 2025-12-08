<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Resolver;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Resolver\CachedProductImagesResolver;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CachedProductImagesResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_delegates_to_decorated_resolver(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);

        $decorated = $this->prophesize(ProductImagesResolverInterface::class);
        $decorated->resolve($productVariant->reveal())
            ->willReturn(['https://example.com/image1.jpg', 'https://example.com/image2.jpg'])
            ->shouldBeCalledOnce();

        $resolver = new CachedProductImagesResolver($decorated->reveal());

        $result = $resolver->resolve($productVariant->reveal());

        self::assertSame(['https://example.com/image1.jpg', 'https://example.com/image2.jpg'], $result);
    }

    /**
     * @test
     */
    public function it_caches_results_for_same_object(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $revealedVariant = $productVariant->reveal();

        $decorated = $this->prophesize(ProductImagesResolverInterface::class);
        $decorated->resolve($revealedVariant)
            ->willReturn(['https://example.com/image.jpg'])
            ->shouldBeCalledOnce();

        $resolver = new CachedProductImagesResolver($decorated->reveal());

        $result1 = $resolver->resolve($revealedVariant);
        $result2 = $resolver->resolve($revealedVariant);
        $result3 = $resolver->resolve($revealedVariant);

        self::assertSame(['https://example.com/image.jpg'], $result1);
        self::assertSame(['https://example.com/image.jpg'], $result2);
        self::assertSame(['https://example.com/image.jpg'], $result3);
    }

    /**
     * @test
     */
    public function it_calls_decorated_resolver_for_different_objects(): void
    {
        $productVariant1 = $this->prophesize(ProductVariantInterface::class);
        $productVariant2 = $this->prophesize(ProductVariantInterface::class);

        $decorated = $this->prophesize(ProductImagesResolverInterface::class);
        $decorated->resolve($productVariant1->reveal())
            ->willReturn(['https://example.com/image1.jpg'])
            ->shouldBeCalledOnce();
        $decorated->resolve($productVariant2->reveal())
            ->willReturn(['https://example.com/image2.jpg'])
            ->shouldBeCalledOnce();

        $resolver = new CachedProductImagesResolver($decorated->reveal());

        $result1 = $resolver->resolve($productVariant1->reveal());
        $result2 = $resolver->resolve($productVariant2->reveal());

        self::assertSame(['https://example.com/image1.jpg'], $result1);
        self::assertSame(['https://example.com/image2.jpg'], $result2);
    }

    /**
     * @test
     */
    public function it_works_with_product_interface(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $revealedProduct = $product->reveal();

        $decorated = $this->prophesize(ProductImagesResolverInterface::class);
        $decorated->resolve($revealedProduct)
            ->willReturn(['https://example.com/product-image.jpg'])
            ->shouldBeCalledOnce();

        $resolver = new CachedProductImagesResolver($decorated->reveal());

        $result1 = $resolver->resolve($revealedProduct);
        $result2 = $resolver->resolve($revealedProduct);

        self::assertSame(['https://example.com/product-image.jpg'], $result1);
        self::assertSame(['https://example.com/product-image.jpg'], $result2);
    }

    /**
     * @test
     */
    public function it_caches_empty_results(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $revealedVariant = $productVariant->reveal();

        $decorated = $this->prophesize(ProductImagesResolverInterface::class);
        $decorated->resolve($revealedVariant)
            ->willReturn([])
            ->shouldBeCalledOnce();

        $resolver = new CachedProductImagesResolver($decorated->reveal());

        $result1 = $resolver->resolve($revealedVariant);
        $result2 = $resolver->resolve($revealedVariant);

        self::assertSame([], $result1);
        self::assertSame([], $result2);
    }
}
