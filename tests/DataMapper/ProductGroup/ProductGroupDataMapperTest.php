<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\ProductGroup;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapper;
use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductGroupDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_product_data_to_product_group(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Test Product');
        $product->getDescription()->willReturn('A great product description');
        $product->getCode()->willReturn('PRODUCT-001');
        $product->getSlug()->willReturn('test-product');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => 'test-product'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://example.com/products/test-product');

        $productGroup = new ProductGroup();

        $mapper = new ProductGroupDataMapper($urlGenerator->reveal());
        $mapper->map($product->reveal(), $productGroup);

        self::assertSame('Test Product', $productGroup->getProperty('name'));
        self::assertSame('A great product description', $productGroup->getProperty('description'));
        self::assertSame('PRODUCT-001', $productGroup->getProperty('productGroupID'));
        self::assertSame('https://example.com/products/test-product', $productGroup->getProperty('url'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_truncates_description_to_5000_characters(): void
    {
        $longDescription = str_repeat('A', 6000);

        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Test Product');
        $product->getDescription()->willReturn($longDescription);
        $product->getCode()->willReturn('PRODUCT-001');
        $product->getSlug()->willReturn('test-product');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => 'test-product'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://example.com/products/test-product');

        $productGroup = new ProductGroup();

        $mapper = new ProductGroupDataMapper($urlGenerator->reveal());
        $mapper->map($product->reveal(), $productGroup);

        $description = $productGroup->getProperty('description');
        self::assertIsString($description);
        self::assertLessThanOrEqual(5000, strlen($description));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_strips_html_tags_from_description(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Test Product');
        $product->getDescription()->willReturn('<p>Product <strong>description</strong></p>');
        $product->getCode()->willReturn('PRODUCT-001');
        $product->getSlug()->willReturn('test-product');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => 'test-product'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://example.com/products/test-product');

        $productGroup = new ProductGroup();

        $mapper = new ProductGroupDataMapper($urlGenerator->reveal());
        $mapper->map($product->reveal(), $productGroup);

        self::assertSame('Product description', $productGroup->getProperty('description'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_null_description(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Test Product');
        $product->getDescription()->willReturn(null);
        $product->getCode()->willReturn('PRODUCT-001');
        $product->getSlug()->willReturn('test-product');

        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(
            'sylius_shop_product_show',
            ['slug' => 'test-product'],
            UrlGeneratorInterface::ABSOLUTE_URL,
        )->willReturn('https://example.com/products/test-product');

        $productGroup = new ProductGroup();

        $mapper = new ProductGroupDataMapper($urlGenerator->reveal());
        $mapper->map($product->reveal(), $productGroup);

        // Verifies the mapper doesn't throw when description is null
        self::assertSame('Test Product', $productGroup->getProperty('name'));
        self::assertSame('PRODUCT-001', $productGroup->getProperty('productGroupID'));
    }
}
