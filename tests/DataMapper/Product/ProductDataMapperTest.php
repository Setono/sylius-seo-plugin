<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\Product;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapper;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductDataMapperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_maps_variant_data_to_product(): void
    {
        $syliusProduct = $this->prophesize(ProductInterface::class);
        $syliusProduct->getDescription()->willReturn('A great product description');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getName()->willReturn('Product Variant Name');
        $productVariant->getCode()->willReturn('VARIANT-001');
        $productVariant->getProduct()->willReturn($syliusProduct->reveal());

        $product = new Product();

        $mapper = new ProductDataMapper();
        $mapper->map($productVariant->reveal(), $product);

        self::assertSame('Product Variant Name', $product->getProperty('name'));
        self::assertSame('A great product description', $product->getProperty('description'));
        self::assertSame('VARIANT-001', $product->getProperty('sku'));
    }

    /**
     * @test
     */
    public function it_falls_back_to_product_name_when_variant_has_no_name(): void
    {
        $syliusProduct = $this->prophesize(ProductInterface::class);
        $syliusProduct->getName()->willReturn('Product Name');
        $syliusProduct->getDescription()->willReturn('Description');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getName()->willReturn(null);
        $productVariant->getCode()->willReturn('VARIANT-001');
        $productVariant->getProduct()->willReturn($syliusProduct->reveal());

        $product = new Product();

        $mapper = new ProductDataMapper();
        $mapper->map($productVariant->reveal(), $product);

        self::assertSame('Product Name', $product->getProperty('name'));
    }

    /**
     * @test
     */
    public function it_handles_null_product(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getName()->willReturn('Variant Name');
        $productVariant->getCode()->willReturn('VARIANT-001');
        $productVariant->getProduct()->willReturn(null);

        $product = new Product();

        $mapper = new ProductDataMapper();
        $mapper->map($productVariant->reveal(), $product);

        // Verifies the mapper doesn't throw when product is null
        self::assertSame('Variant Name', $product->getProperty('name'));
        self::assertSame('VARIANT-001', $product->getProperty('sku'));
    }

    /**
     * @test
     */
    public function it_truncates_name_to_70_characters(): void
    {
        $longName = str_repeat('A', 100);

        $syliusProduct = $this->prophesize(ProductInterface::class);
        $syliusProduct->getDescription()->willReturn('Description');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getName()->willReturn($longName);
        $productVariant->getCode()->willReturn('CODE');
        $productVariant->getProduct()->willReturn($syliusProduct->reveal());

        $product = new Product();

        $mapper = new ProductDataMapper();
        $mapper->map($productVariant->reveal(), $product);

        $name = $product->getProperty('name');
        self::assertIsString($name);
        self::assertLessThanOrEqual(70, strlen($name));
    }

    /**
     * @test
     */
    public function it_truncates_description_to_5000_characters(): void
    {
        $longDescription = str_repeat('A', 6000);

        $syliusProduct = $this->prophesize(ProductInterface::class);
        $syliusProduct->getDescription()->willReturn($longDescription);

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getName()->willReturn('Name');
        $productVariant->getCode()->willReturn('CODE');
        $productVariant->getProduct()->willReturn($syliusProduct->reveal());

        $product = new Product();

        $mapper = new ProductDataMapper();
        $mapper->map($productVariant->reveal(), $product);

        $description = $product->getProperty('description');
        self::assertIsString($description);
        self::assertLessThanOrEqual(5000, strlen($description));
    }

    /**
     * @test
     */
    public function it_strips_html_tags_from_description(): void
    {
        $syliusProduct = $this->prophesize(ProductInterface::class);
        $syliusProduct->getDescription()->willReturn('<p>Product <strong>description</strong></p>');

        $productVariant = $this->prophesize(ProductVariantInterface::class);
        $productVariant->getName()->willReturn('Name');
        $productVariant->getCode()->willReturn('CODE');
        $productVariant->getProduct()->willReturn($syliusProduct->reveal());

        $product = new Product();

        $mapper = new ProductDataMapper();
        $mapper->map($productVariant->reveal(), $product);

        self::assertSame('Product description', $product->getProperty('description'));
    }
}
