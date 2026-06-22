<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\EventSubscriber\StructuredData;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\DataMapper\ProductGroup\ProductGroupDataMapperInterface;
use Setono\SyliusSEOPlugin\Event\ProductAddedToGraph;
use Setono\SyliusSEOPlugin\Event\ProductGroupAddedToGraph;
use Setono\SyliusSEOPlugin\EventSubscriber\StructuredData\AddProductSubscriber;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Product;
use Spatie\SchemaOrg\ProductGroup;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class AddProductSubscriberTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function it_subscribes_to_product_show(): void
    {
        $events = AddProductSubscriber::getSubscribedEvents();

        self::assertArrayHasKey('sylius.product.show', $events);
        self::assertSame('populate', $events['sylius.product.show']);
    }

    #[Test]
    public function it_handles_simple_product(): void
    {
        $variant = $this->prophesize(ProductVariantInterface::class);

        $product = $this->prophesize(ProductInterface::class);
        $product->isSimple()->willReturn(true);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$variant->reveal()]));

        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map($variant->reveal(), Argument::type(Product::class))->shouldBeCalled();

        $productGroupDataMapper = $this->prophesize(ProductGroupDataMapperInterface::class);
        $productGroupDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(ProductAddedToGraph::class))->shouldBeCalled();

        $subscriber = new AddProductSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productGroupDataMapper->reveal(),
            $eventDispatcher->reveal(),
        );

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($product->reveal());

        $subscriber->populate($event->reveal());
    }

    #[Test]
    public function it_handles_configurable_product(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->isSimple()->willReturn(false);

        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $productGroupDataMapper = $this->prophesize(ProductGroupDataMapperInterface::class);
        $productGroupDataMapper->map($product->reveal(), Argument::type(ProductGroup::class))->shouldBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(ProductGroupAddedToGraph::class))->shouldBeCalled();

        $subscriber = new AddProductSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productGroupDataMapper->reveal(),
            $eventDispatcher->reveal(),
        );

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($product->reveal());

        $subscriber->populate($event->reveal());
    }

    #[Test]
    public function it_does_not_map_simple_product_without_enabled_variant(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->isSimple()->willReturn(true);
        $product->getEnabledVariants()->willReturn(new ArrayCollection());

        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $productGroupDataMapper = $this->prophesize(ProductGroupDataMapperInterface::class);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();

        $subscriber = new AddProductSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productGroupDataMapper->reveal(),
            $eventDispatcher->reveal(),
        );

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($product->reveal());

        $subscriber->populate($event->reveal());
    }
}
