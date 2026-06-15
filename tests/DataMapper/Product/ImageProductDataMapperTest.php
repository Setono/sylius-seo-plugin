<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\DataMapper\Product;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusSEOPlugin\DataMapper\Product\ImageProductDataMapper;
use Setono\SyliusSEOPlugin\Resolver\ProductImagesResolverInterface;
use Spatie\SchemaOrg\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ImageProductDataMapperTest extends TestCase
{
    use ProphecyTrait;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_images_from_resolver(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);

        $productImagesResolver = $this->prophesize(ProductImagesResolverInterface::class);
        $productImagesResolver->resolve($productVariant->reveal())
            ->willReturn([
                'https://example.com/media/cache/path1.jpg',
                'https://example.com/media/cache/path2.jpg',
                'https://example.com/media/cache/path3.jpg',
            ]);

        $product = new Product();

        $mapper = new ImageProductDataMapper($productImagesResolver->reveal());
        $mapper->map($productVariant->reveal(), $product);

        self::assertSame([
            'https://example.com/media/cache/path1.jpg',
            'https://example.com/media/cache/path2.jpg',
            'https://example.com/media/cache/path3.jpg',
        ], $product->getProperty('image'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_map_when_image_property_already_set(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);

        $productImagesResolver = $this->prophesize(ProductImagesResolverInterface::class);
        $productImagesResolver->resolve($productVariant->reveal())->shouldNotBeCalled();

        $product = new Product();
        $product->image('https://example.com/existing-image.jpg');

        $mapper = new ImageProductDataMapper($productImagesResolver->reveal());
        $mapper->map($productVariant->reveal(), $product);

        self::assertSame('https://example.com/existing-image.jpg', $product->getProperty('image'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_maps_empty_array_when_no_images_available(): void
    {
        $productVariant = $this->prophesize(ProductVariantInterface::class);

        $productImagesResolver = $this->prophesize(ProductImagesResolverInterface::class);
        $productImagesResolver->resolve($productVariant->reveal())->willReturn([]);

        $product = new Product();

        $mapper = new ImageProductDataMapper($productImagesResolver->reveal());
        $mapper->map($productVariant->reveal(), $product);

        self::assertSame([], $product->getProperty('image'));
    }
}
