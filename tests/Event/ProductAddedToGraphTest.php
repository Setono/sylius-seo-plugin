<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Event;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Event\ProductAddedToGraph;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

final class ProductAddedToGraphTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_holds_product_only(): void
    {
        $product = new Product();

        $event = new ProductAddedToGraph($product);

        self::assertSame($product, $event->product);
        self::assertNull($event->storeProduct);
        self::assertNull($event->storeProductVariant);
    }

    #[Test]
    public function it_holds_product_with_store_product(): void
    {
        $product = new Product();
        $storeProduct = $this->prophesize(ProductInterface::class);

        $event = new ProductAddedToGraph($product, $storeProduct->reveal());

        self::assertSame($product, $event->product);
        self::assertSame($storeProduct->reveal(), $event->storeProduct);
        self::assertNull($event->storeProductVariant);
    }

    #[Test]
    public function it_holds_product_with_store_product_variant(): void
    {
        $product = new Product();
        $storeProduct = $this->prophesize(ProductInterface::class);
        $storeProductVariant = $this->prophesize(ProductVariantInterface::class);

        $event = new ProductAddedToGraph($product, $storeProduct->reveal(), $storeProductVariant->reveal());

        self::assertSame($product, $event->product);
        self::assertSame($storeProduct->reveal(), $event->storeProduct);
        self::assertSame($storeProductVariant->reveal(), $event->storeProductVariant);
    }

    #[Test]
    public function it_holds_product_with_only_variant(): void
    {
        $product = new Product();
        $storeProductVariant = $this->prophesize(ProductVariantInterface::class);

        $event = new ProductAddedToGraph($product, null, $storeProductVariant->reveal());

        self::assertSame($product, $event->product);
        self::assertNull($event->storeProduct);
        self::assertSame($storeProductVariant->reveal(), $event->storeProductVariant);
    }
}
