<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\ProductGroup;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\HasVariantProductGroupDataMapper;
use Spatie\SchemaOrg\Product;
use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class HasVariantProductGroupDataMapperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_maps_all_enabled_variants_to_product_group(): void
    {
        $variant1 = $this->prophesize(ProductVariantInterface::class);
        $variant2 = $this->prophesize(ProductVariantInterface::class);

        $product = $this->prophesize(ProductInterface::class);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([
            $variant1->reveal(),
            $variant2->reveal(),
        ]));

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map($variant1->reveal(), Argument::type(Product::class))->shouldBeCalled();
        $productDataMapper->map($variant2->reveal(), Argument::type(Product::class))->shouldBeCalled();

        $productGroup = new ProductGroup();

        $mapper = new HasVariantProductGroupDataMapper($productDataMapper->reveal());
        $mapper->map($product->reveal(), $productGroup);

        $hasVariant = $productGroup->getProperty('hasVariant');
        self::assertIsArray($hasVariant);
        self::assertCount(2, $hasVariant);
    }

    /**
     * @test
     */
    public function it_handles_product_with_no_variants(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([]));

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $productGroup = new ProductGroup();

        $mapper = new HasVariantProductGroupDataMapper($productDataMapper->reveal());
        $mapper->map($product->reveal(), $productGroup);

        $hasVariant = $productGroup->getProperty('hasVariant');
        self::assertIsArray($hasVariant);
        self::assertEmpty($hasVariant);
    }

    /**
     * @test
     */
    public function it_maps_single_variant(): void
    {
        $variant = $this->prophesize(ProductVariantInterface::class);

        $product = $this->prophesize(ProductInterface::class);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([
            $variant->reveal(),
        ]));

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map($variant->reveal(), Argument::type(Product::class))->shouldBeCalled();

        $productGroup = new ProductGroup();

        $mapper = new HasVariantProductGroupDataMapper($productDataMapper->reveal());
        $mapper->map($product->reveal(), $productGroup);

        $hasVariant = $productGroup->getProperty('hasVariant');
        self::assertIsArray($hasVariant);
        self::assertCount(1, $hasVariant);
    }
}
