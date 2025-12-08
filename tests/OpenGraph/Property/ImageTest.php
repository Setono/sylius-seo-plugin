<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Property;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Image;

final class ImageTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_image_with_url(): void
    {
        $image = new Image('https://example.com/image.jpg');

        self::assertSame('https://example.com/image.jpg', $image->url);
        self::assertNull($image->secureUrl);
        self::assertNull($image->type);
        self::assertNull($image->width);
        self::assertNull($image->height);
        self::assertNull($image->alt);
    }

    /**
     * @test
     */
    public function it_creates_image_using_static_factory(): void
    {
        $image = Image::create('https://example.com/image.jpg');

        self::assertSame('https://example.com/image.jpg', $image->url);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_secure_url(): void
    {
        $original = new Image('https://example.com/image.jpg');
        $modified = $original->withSecureUrl('https://secure.example.com/image.jpg');

        self::assertNotSame($original, $modified);
        self::assertNull($original->secureUrl);
        self::assertSame('https://secure.example.com/image.jpg', $modified->secureUrl);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_type(): void
    {
        $original = new Image('https://example.com/image.jpg');
        $modified = $original->withType('image/jpeg');

        self::assertNotSame($original, $modified);
        self::assertNull($original->type);
        self::assertSame('image/jpeg', $modified->type);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_width(): void
    {
        $original = new Image('https://example.com/image.jpg');
        $modified = $original->withWidth(1200);

        self::assertNotSame($original, $modified);
        self::assertNull($original->width);
        self::assertSame(1200, $modified->width);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_height(): void
    {
        $original = new Image('https://example.com/image.jpg');
        $modified = $original->withHeight(630);

        self::assertNotSame($original, $modified);
        self::assertNull($original->height);
        self::assertSame(630, $modified->height);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_dimensions(): void
    {
        $original = new Image('https://example.com/image.jpg');
        $modified = $original->withDimensions(1200, 630);

        self::assertNotSame($original, $modified);
        self::assertNull($original->width);
        self::assertNull($original->height);
        self::assertSame(1200, $modified->width);
        self::assertSame(630, $modified->height);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_alt(): void
    {
        $original = new Image('https://example.com/image.jpg');
        $modified = $original->withAlt('A beautiful sunset');

        self::assertNotSame($original, $modified);
        self::assertNull($original->alt);
        self::assertSame('A beautiful sunset', $modified->alt);
    }

    /**
     * @test
     */
    public function it_supports_fluent_chaining(): void
    {
        $image = Image::create('https://example.com/image.jpg')
            ->withSecureUrl('https://secure.example.com/image.jpg')
            ->withType('image/jpeg')
            ->withDimensions(1200, 630)
            ->withAlt('Product image');

        self::assertSame('https://example.com/image.jpg', $image->url);
        self::assertSame('https://secure.example.com/image.jpg', $image->secureUrl);
        self::assertSame('image/jpeg', $image->type);
        self::assertSame(1200, $image->width);
        self::assertSame(630, $image->height);
        self::assertSame('Product image', $image->alt);
    }

    /**
     * @test
     */
    public function it_converts_to_array_with_only_url(): void
    {
        $image = new Image('https://example.com/image.jpg');

        self::assertSame(['og:image' => 'https://example.com/image.jpg'], $image->toArray());
    }

    /**
     * @test
     */
    public function it_converts_to_array_with_all_properties(): void
    {
        $image = new Image(
            'https://example.com/image.jpg',
            'https://secure.example.com/image.jpg',
            'image/jpeg',
            1200,
            630,
            'Product image',
        );

        $expected = [
            'og:image' => 'https://example.com/image.jpg',
            'og:image:secure_url' => 'https://secure.example.com/image.jpg',
            'og:image:type' => 'image/jpeg',
            'og:image:width' => 1200,
            'og:image:height' => 630,
            'og:image:alt' => 'Product image',
        ];

        self::assertSame($expected, $image->toArray());
    }
}
