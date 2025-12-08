<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Property;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\Property\Video;

final class VideoTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_video_with_url(): void
    {
        $video = new Video('https://example.com/video.mp4');

        self::assertSame('https://example.com/video.mp4', $video->url);
        self::assertNull($video->secureUrl);
        self::assertNull($video->type);
        self::assertNull($video->width);
        self::assertNull($video->height);
    }

    /**
     * @test
     */
    public function it_creates_video_using_static_factory(): void
    {
        $video = Video::create('https://example.com/video.mp4');

        self::assertSame('https://example.com/video.mp4', $video->url);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_secure_url(): void
    {
        $original = new Video('https://example.com/video.mp4');
        $modified = $original->withSecureUrl('https://secure.example.com/video.mp4');

        self::assertNotSame($original, $modified);
        self::assertNull($original->secureUrl);
        self::assertSame('https://secure.example.com/video.mp4', $modified->secureUrl);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_type(): void
    {
        $original = new Video('https://example.com/video.mp4');
        $modified = $original->withType('video/mp4');

        self::assertNotSame($original, $modified);
        self::assertNull($original->type);
        self::assertSame('video/mp4', $modified->type);
    }

    /**
     * @test
     */
    public function it_returns_immutable_copy_with_dimensions(): void
    {
        $original = new Video('https://example.com/video.mp4');
        $modified = $original->withDimensions(1920, 1080);

        self::assertNotSame($original, $modified);
        self::assertNull($original->width);
        self::assertNull($original->height);
        self::assertSame(1920, $modified->width);
        self::assertSame(1080, $modified->height);
    }

    /**
     * @test
     */
    public function it_supports_fluent_chaining(): void
    {
        $video = Video::create('https://example.com/video.mp4')
            ->withSecureUrl('https://secure.example.com/video.mp4')
            ->withType('video/mp4')
            ->withDimensions(1920, 1080);

        self::assertSame('https://example.com/video.mp4', $video->url);
        self::assertSame('https://secure.example.com/video.mp4', $video->secureUrl);
        self::assertSame('video/mp4', $video->type);
        self::assertSame(1920, $video->width);
        self::assertSame(1080, $video->height);
    }

    /**
     * @test
     */
    public function it_converts_to_array_with_only_url(): void
    {
        $video = new Video('https://example.com/video.mp4');

        self::assertSame(['og:video' => 'https://example.com/video.mp4'], $video->toArray());
    }

    /**
     * @test
     */
    public function it_converts_to_array_with_all_properties(): void
    {
        $video = new Video(
            'https://example.com/video.mp4',
            'https://secure.example.com/video.mp4',
            'video/mp4',
            1920,
            1080,
        );

        $expected = [
            'og:video' => 'https://example.com/video.mp4',
            'og:video:secure_url' => 'https://secure.example.com/video.mp4',
            'og:video:type' => 'video/mp4',
            'og:video:width' => 1920,
            'og:video:height' => 1080,
        ];

        self::assertSame($expected, $video->toArray());
    }
}
