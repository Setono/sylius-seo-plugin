<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\EventSubscriber\OpenGraph;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\EventSubscriber\OpenGraph\AddProductInformationSubscriber;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;

final class AddProductInformationSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_subscribes_to_product_show(): void
    {
        $events = AddProductInformationSubscriber::getSubscribedEvents();

        self::assertArrayHasKey('sylius.product.show', $events);
        self::assertSame('add', $events['sylius.product.show']);
    }

    /**
     * @test
     */
    public function it_adds_product_information_with_image(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Cool Product');
        $product->getDescription()->willReturn('A cool description');

        $productImagesResolver = $this->prophesize(ProductImagesResolverInterface::class);
        $productImagesResolver->resolve($product->reveal())
            ->willReturn(['https://example.com/image.jpg']);

        $openGraph = new OpenGraph();

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($product->reveal());

        $subscriber = new AddProductInformationSubscriber(
            $productImagesResolver->reveal(),
            $openGraph,
        );

        $subscriber->add($event->reveal());

        self::assertSame('A cool description', $openGraph->getDescription());
        self::assertCount(1, $openGraph->getImages());
    }

    /**
     * @test
     */
    public function it_adds_product_information_without_image(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getDescription()->willReturn('A product without images');

        $productImagesResolver = $this->prophesize(ProductImagesResolverInterface::class);
        $productImagesResolver->resolve($product->reveal())->willReturn([]);

        $openGraph = new OpenGraph();

        $event = $this->prophesize(ResourceControllerEvent::class);
        $event->getSubject()->willReturn($product->reveal());

        $subscriber = new AddProductInformationSubscriber(
            $productImagesResolver->reveal(),
            $openGraph,
        );

        $subscriber->add($event->reveal());

        self::assertSame('A product without images', $openGraph->getDescription());
        self::assertCount(0, $openGraph->getImages());
    }
}
