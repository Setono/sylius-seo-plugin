<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Video;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Video\Movie;

final class MovieTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $movie = new Movie();

        self::assertSame('video.movie', $movie->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_html_by_default(): void
    {
        $movie = new Movie();

        self::assertSame('', $movie->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_actors(): void
    {
        $movie = new Movie(actors: [
            'https://example.com/actor/1',
            'https://example.com/actor/2',
        ]);

        $expected = '<meta property="video:actor" content="https://example.com/actor/1">' . "\n" .
            '<meta property="video:actor" content="https://example.com/actor/2">';

        self::assertSame($expected, $movie->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_directors(): void
    {
        $movie = new Movie(directors: ['https://example.com/director/1']);

        self::assertSame('<meta property="video:director" content="https://example.com/director/1">', $movie->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_writers(): void
    {
        $movie = new Movie(writers: ['https://example.com/writer/1']);

        self::assertSame('<meta property="video:writer" content="https://example.com/writer/1">', $movie->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_duration(): void
    {
        $movie = new Movie(duration: 7200);

        self::assertSame('<meta property="video:duration" content="7200">', $movie->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-03-15T00:00:00+00:00');
        $movie = new Movie(releaseDate: $date);

        self::assertSame('<meta property="video:release_date" content="2024-03-15T00:00:00+00:00">', $movie->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_tags(): void
    {
        $movie = new Movie(tags: ['action', 'thriller']);

        $expected = '<meta property="video:tag" content="action">' . "\n" .
            '<meta property="video:tag" content="thriller">';

        self::assertSame($expected, $movie->toHtml());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $releaseDate = new \DateTimeImmutable('2024-03-15T00:00:00+00:00');

        $og = (new OpenGraph())
            ->title('The Great Movie')
            ->type(new Movie(
                actors: ['https://example.com/actor/1'],
                directors: ['https://example.com/director/1'],
                duration: 7200,
                releaseDate: $releaseDate,
                tags: ['action'],
            ));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="The Great Movie">', $html);
        self::assertStringContainsString('<meta property="og:type" content="video.movie">', $html);
        self::assertStringContainsString('<meta property="video:actor" content="https://example.com/actor/1">', $html);
        self::assertStringContainsString('<meta property="video:director" content="https://example.com/director/1">', $html);
        self::assertStringContainsString('<meta property="video:duration" content="7200">', $html);
        self::assertStringContainsString('<meta property="video:release_date" content="2024-03-15T00:00:00+00:00">', $html);
        self::assertStringContainsString('<meta property="video:tag" content="action">', $html);
    }
}
