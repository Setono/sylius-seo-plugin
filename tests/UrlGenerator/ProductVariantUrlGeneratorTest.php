<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\UrlGenerator;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\UrlGenerator\ProductVariantUrlGenerator;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductVariantUrlGeneratorTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_generates_absolute_url_for_product_variant(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getSlug()->willReturn('cool-product');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn($product->reveal());

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => 'cool-product'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://example.com/products/cool-product');

        $generator = new ProductVariantUrlGenerator($urlGenerator->reveal());

        $result = $generator->generate($productVariant->reveal());

        self::assertSame('https://example.com/products/cool-product', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_variant_without_product(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn(null);

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => null],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://example.com/products/');

        $generator = new ProductVariantUrlGenerator($urlGenerator->reveal());

        $result = $generator->generate($productVariant->reveal());

        self::assertSame('https://example.com/products/', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_product_without_slug(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getSlug()->willReturn(null);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getProduct()->willReturn($product->reveal());

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => null],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://example.com/products/');

        $generator = new ProductVariantUrlGenerator($urlGenerator->reveal());

        $result = $generator->generate($productVariant->reveal());

        self::assertSame('https://example.com/products/', $result);
    }
}
