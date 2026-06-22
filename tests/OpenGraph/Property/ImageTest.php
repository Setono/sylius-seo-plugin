<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Property;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Image;

final class ImageTest extends TestCase
{
    #[Test]
    public function it_creates_image_with_url(): void
    {
        $image = new Image('https://example.com/image.jpg');

        self::assertSame('https://example.com/image.jpg', $image->getUrl());
        self::assertNull($image->getSecureUrl());
        self::assertNull($image->getType());
        self::assertNull($image->getWidth());
        self::assertNull($image->getHeight());
        self::assertNull($image->getAlt());
    }

    #[Test]
    public function it_sets_url(): void
    {
        $image = new Image('https://example.com/image.jpg');
        $image->url('https://example.com/other.jpg');

        self::assertSame('https://example.com/other.jpg', $image->getUrl());
    }

    #[Test]
    public function it_sets_secure_url(): void
    {
        $image = new Image('https://example.com/image.jpg');
        $image->secureUrl('https://secure.example.com/image.jpg');

        self::assertSame('https://secure.example.com/image.jpg', $image->getSecureUrl());
    }

    #[Test]
    public function it_sets_type(): void
    {
        $image = new Image('https://example.com/image.jpg');
        $image->type('image/jpeg');

        self::assertSame('image/jpeg', $image->getType());
    }

    #[Test]
    public function it_sets_width(): void
    {
        $image = new Image('https://example.com/image.jpg');
        $image->width(1200);

        self::assertSame(1200, $image->getWidth());
    }

    #[Test]
    public function it_sets_height(): void
    {
        $image = new Image('https://example.com/image.jpg');
        $image->height(630);

        self::assertSame(630, $image->getHeight());
    }

    #[Test]
    public function it_sets_dimensions(): void
    {
        $image = new Image('https://example.com/image.jpg');
        $image->dimensions(1200, 630);

        self::assertSame(1200, $image->getWidth());
        self::assertSame(630, $image->getHeight());
    }

    #[Test]
    public function it_sets_alt(): void
    {
        $image = new Image('https://example.com/image.jpg');
        $image->alt('A beautiful sunset');

        self::assertSame('A beautiful sunset', $image->getAlt());
    }

    #[Test]
    public function it_supports_fluent_chaining(): void
    {
        $image = (new Image('https://example.com/image.jpg'))
            ->secureUrl('https://secure.example.com/image.jpg')
            ->type('image/jpeg')
            ->dimensions(1200, 630)
            ->alt('Product image');

        self::assertSame('https://example.com/image.jpg', $image->getUrl());
        self::assertSame('https://secure.example.com/image.jpg', $image->getSecureUrl());
        self::assertSame('image/jpeg', $image->getType());
        self::assertSame(1200, $image->getWidth());
        self::assertSame(630, $image->getHeight());
        self::assertSame('Product image', $image->getAlt());
    }

    #[Test]
    public function it_renders_html_with_only_url(): void
    {
        $image = new Image('https://example.com/image.jpg');

        self::assertSame('<meta property="og:image" content="https://example.com/image.jpg">', $image->toHtml());
    }

    #[Test]
    public function it_renders_html_with_all_properties(): void
    {
        $image = (new Image('https://example.com/image.jpg'))
            ->secureUrl('https://secure.example.com/image.jpg')
            ->type('image/jpeg')
            ->dimensions(1200, 630)
            ->alt('Product image');

        $expected = '<meta property="og:image" content="https://example.com/image.jpg">' . "\n" .
            '<meta property="og:image:secure_url" content="https://secure.example.com/image.jpg">' . "\n" .
            '<meta property="og:image:type" content="image/jpeg">' . "\n" .
            '<meta property="og:image:width" content="1200">' . "\n" .
            '<meta property="og:image:height" content="630">' . "\n" .
            '<meta property="og:image:alt" content="Product image">';

        self::assertSame($expected, $image->toHtml());
    }
}
