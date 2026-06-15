<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\EventSubscriber\OpenGraph;

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function it_subscribes_to_product_show(): void
    {
        $events = AddProductInformationSubscriber::getSubscribedEvents();

        self::assertArrayHasKey('sylius.product.show', $events);
        self::assertSame('add', $events['sylius.product.show']);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function it_does_not_set_description_when_null(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getDescription()->willReturn(null);

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

        self::assertNull($openGraph->getDescription());
    }

    #[Test]
    public function it_strips_html_tags_from_description(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getDescription()->willReturn('<p>This is a <strong>bold</strong> description with <a href="#">links</a>.</p>');

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

        self::assertSame('This is a bold description with links.', $openGraph->getDescription());
    }

    #[Test]
    public function it_normalizes_whitespace_in_description(): void
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getDescription()->willReturn("This   has   multiple   spaces\n\nand\nnewlines\t\ttabs");

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

        self::assertSame('This has multiple spaces and newlines tabs', $openGraph->getDescription());
    }

    #[Test]
    public function it_truncates_long_description_to_around_300_characters(): void
    {
        // Create a long description with words so truncation can occur at word boundaries
        $longDescription = str_repeat('word ', 100); // 500 characters

        $product = $this->prophesize(ProductInterface::class);
        $product->getDescription()->willReturn($longDescription);

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

        $description = $openGraph->getDescription();
        self::assertNotNull($description);
        // truncate(300, '...', false) cuts at word boundary near 300 chars then adds "..."
        self::assertStringEndsWith('...', $description);
        self::assertLessThanOrEqual(303, strlen($description)); // max 300 + "..."
    }

    #[Test]
    public function it_does_not_truncate_description_at_300_characters(): void
    {
        $exactDescription = str_repeat('B', 300);

        $product = $this->prophesize(ProductInterface::class);
        $product->getDescription()->willReturn($exactDescription);

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

        self::assertSame($exactDescription, $openGraph->getDescription());
    }

    #[Test]
    public function it_sanitizes_complex_html_description(): void
    {
        $htmlDescription = '<div class="product-description">
            <h2>Product Features</h2>
            <ul>
                <li>Feature 1</li>
                <li>Feature 2</li>
            </ul>
            <p>Buy now!</p>
        </div>';

        $product = $this->prophesize(ProductInterface::class);
        $product->getDescription()->willReturn($htmlDescription);

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

        $description = $openGraph->getDescription();
        self::assertNotNull($description);
        // Check that HTML is stripped and whitespace is normalized
        self::assertStringContainsString('Product Features', $description);
        self::assertStringContainsString('Feature 1', $description);
        self::assertStringContainsString('Feature 2', $description);
        self::assertStringContainsString('Buy now!', $description);
        self::assertStringNotContainsString('<', $description);
        self::assertStringNotContainsString('>', $description);
    }
}
