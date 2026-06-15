<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\EventSubscriber\StructuredData;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\Product\ProductDataMapperInterface;
use Setono\SyliusSEOPlugin\EventSubscriber\StructuredData\AddItemListSubscriber;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Product;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class AddItemListSubscriberTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_subscribes_to_product_index(): void
    {
        $events = AddItemListSubscriber::getSubscribedEvents();

        self::assertArrayHasKey('sylius.product.index', $events);
        self::assertSame('populate', $events['sylius.product.index']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_nothing_when_subject_is_not_resource_grid_view(): void
    {
        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);

        $subscriber = new AddItemListSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productVariantResolver->reveal(),
        );

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn(new \stdClass());

        $subscriber->populate($event->reveal());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_nothing_when_data_is_not_iterable(): void
    {
        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);

        $subscriber = new AddItemListSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productVariantResolver->reveal(),
        );

        $gridView = $this->prophesize(ResourceGridView::class);
        $gridView->getData()->willReturn('not-iterable');

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($gridView->reveal());

        $subscriber->populate($event->reveal());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_populates_item_list_with_products(): void
    {
        $variant1 = $this->prophesize(ProductVariantInterface::class);
        $variant2 = $this->prophesize(ProductVariantInterface::class);

        $product1 = $this->prophesize(ProductInterface::class);
        $product2 = $this->prophesize(ProductInterface::class);

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);
        $productVariantResolver->getVariant($product1->reveal())->willReturn($variant1->reveal());
        $productVariantResolver->getVariant($product2->reveal())->willReturn($variant2->reveal());

        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map($variant1->reveal(), Argument::type(Product::class))->shouldBeCalled();
        $productDataMapper->map($variant2->reveal(), Argument::type(Product::class))->shouldBeCalled();

        $subscriber = new AddItemListSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productVariantResolver->reveal(),
        );

        $gridView = $this->prophesize(ResourceGridView::class);
        $gridView->getData()->willReturn([$product1->reveal(), $product2->reveal()]);

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($gridView->reveal());

        $subscriber->populate($event->reveal());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_skips_products_without_variant(): void
    {
        $variant = $this->prophesize(ProductVariantInterface::class);

        $product1 = $this->prophesize(ProductInterface::class);
        $product2 = $this->prophesize(ProductInterface::class);

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);
        $productVariantResolver->getVariant($product1->reveal())->willReturn($variant->reveal());
        $productVariantResolver->getVariant($product2->reveal())->willReturn(null);

        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map($variant->reveal(), Argument::type(Product::class))->shouldBeCalledOnce();

        $subscriber = new AddItemListSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productVariantResolver->reveal(),
        );

        $gridView = $this->prophesize(ResourceGridView::class);
        $gridView->getData()->willReturn([$product1->reveal(), $product2->reveal()]);

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($gridView->reveal());

        $subscriber->populate($event->reveal());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_skips_non_product_items(): void
    {
        $graph = new Graph();

        $productDataMapper = $this->prophesize(ProductDataMapperInterface::class);
        $productDataMapper->map(Argument::any(), Argument::any())->shouldNotBeCalled();

        $productVariantResolver = $this->prophesize(ProductVariantResolverInterface::class);

        $subscriber = new AddItemListSubscriber(
            $graph,
            $productDataMapper->reveal(),
            $productVariantResolver->reveal(),
        );

        $gridView = $this->prophesize(ResourceGridView::class);
        $gridView->getData()->willReturn([new \stdClass(), 'string', 123]);

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($gridView->reveal());

        $subscriber->populate($event->reveal());
    }
}
