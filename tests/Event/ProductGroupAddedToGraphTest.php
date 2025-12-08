<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Event;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\Event\ProductGroupAddedToGraph;
use Spatie\SchemaOrg\ProductGroup;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductGroupAddedToGraphTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_holds_product_group_only(): void
    {
        $productGroup = new ProductGroup();

        $event = new ProductGroupAddedToGraph($productGroup);

        self::assertSame($productGroup, $event->productGroup);
        self::assertNull($event->storeProduct);
    }

    /**
     * @test
     */
    public function it_holds_product_group_with_store_product(): void
    {
        $productGroup = new ProductGroup();
        $storeProduct = $this->prophesize(ProductInterface::class);

        $event = new ProductGroupAddedToGraph($productGroup, $storeProduct->reveal());

        self::assertSame($productGroup, $event->productGroup);
        self::assertSame($storeProduct->reveal(), $event->storeProduct);
    }
}
