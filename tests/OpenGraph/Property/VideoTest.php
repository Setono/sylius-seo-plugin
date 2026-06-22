<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Property;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Video;

final class VideoTest extends TestCase
{
    #[Test]
    public function it_creates_video_with_url(): void
    {
        $video = new Video('https://example.com/video.mp4');

        self::assertSame('https://example.com/video.mp4', $video->getUrl());
        self::assertNull($video->getSecureUrl());
        self::assertNull($video->getType());
        self::assertNull($video->getWidth());
        self::assertNull($video->getHeight());
    }

    #[Test]
    public function it_sets_url(): void
    {
        $video = new Video('https://example.com/video.mp4');
        $video->url('https://example.com/other.mp4');

        self::assertSame('https://example.com/other.mp4', $video->getUrl());
    }

    #[Test]
    public function it_sets_secure_url(): void
    {
        $video = new Video('https://example.com/video.mp4');
        $video->secureUrl('https://secure.example.com/video.mp4');

        self::assertSame('https://secure.example.com/video.mp4', $video->getSecureUrl());
    }

    #[Test]
    public function it_sets_type(): void
    {
        $video = new Video('https://example.com/video.mp4');
        $video->type('video/mp4');

        self::assertSame('video/mp4', $video->getType());
    }

    #[Test]
    public function it_sets_width(): void
    {
        $video = new Video('https://example.com/video.mp4');
        $video->width(1920);

        self::assertSame(1920, $video->getWidth());
    }

    #[Test]
    public function it_sets_height(): void
    {
        $video = new Video('https://example.com/video.mp4');
        $video->height(1080);

        self::assertSame(1080, $video->getHeight());
    }

    #[Test]
    public function it_sets_dimensions(): void
    {
        $video = new Video('https://example.com/video.mp4');
        $video->dimensions(1920, 1080);

        self::assertSame(1920, $video->getWidth());
        self::assertSame(1080, $video->getHeight());
    }

    #[Test]
    public function it_supports_fluent_chaining(): void
    {
        $video = (new Video('https://example.com/video.mp4'))
            ->secureUrl('https://secure.example.com/video.mp4')
            ->type('video/mp4')
            ->dimensions(1920, 1080);

        self::assertSame('https://example.com/video.mp4', $video->getUrl());
        self::assertSame('https://secure.example.com/video.mp4', $video->getSecureUrl());
        self::assertSame('video/mp4', $video->getType());
        self::assertSame(1920, $video->getWidth());
        self::assertSame(1080, $video->getHeight());
    }

    #[Test]
    public function it_renders_html_with_only_url(): void
    {
        $video = new Video('https://example.com/video.mp4');

        self::assertSame('<meta property="og:video" content="https://example.com/video.mp4">', $video->toHtml());
    }

    #[Test]
    public function it_renders_html_with_all_properties(): void
    {
        $video = (new Video('https://example.com/video.mp4'))
            ->secureUrl('https://secure.example.com/video.mp4')
            ->type('video/mp4')
            ->dimensions(1920, 1080);

        $expected = '<meta property="og:video" content="https://example.com/video.mp4">' . "\n" .
            '<meta property="og:video:secure_url" content="https://secure.example.com/video.mp4">' . "\n" .
            '<meta property="og:video:type" content="video/mp4">' . "\n" .
            '<meta property="og:video:width" content="1920">' . "\n" .
            '<meta property="og:video:height" content="1080">';

        self::assertSame($expected, $video->toHtml());
    }
}
