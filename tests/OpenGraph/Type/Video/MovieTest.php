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
    public function it_returns_empty_properties_by_default(): void
    {
        $movie = new Movie();

        self::assertSame([], $movie->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_actors(): void
    {
        $movie = new Movie(actors: [
            'https://example.com/actor/1',
            'https://example.com/actor/2',
        ]);

        self::assertSame([
            'video:actor' => [
                'https://example.com/actor/1',
                'https://example.com/actor/2',
            ],
        ], $movie->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_directors(): void
    {
        $movie = new Movie(directors: ['https://example.com/director/1']);

        self::assertSame(['video:director' => ['https://example.com/director/1']], $movie->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_writers(): void
    {
        $movie = new Movie(writers: ['https://example.com/writer/1']);

        self::assertSame(['video:writer' => ['https://example.com/writer/1']], $movie->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_duration(): void
    {
        $movie = new Movie(duration: 7200);

        self::assertSame(['video:duration' => 7200], $movie->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-03-15T00:00:00+00:00');
        $movie = new Movie(releaseDate: $date);

        self::assertSame(['video:release_date' => '2024-03-15T00:00:00+00:00'], $movie->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_tags(): void
    {
        $movie = new Movie(tags: ['action', 'thriller']);

        self::assertSame(['video:tag' => ['action', 'thriller']], $movie->getProperties());
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

        $data = $og->toArray();

        self::assertSame('The Great Movie', $data['og:title']);
        self::assertSame('video.movie', $data['og:type']);
        self::assertSame(['https://example.com/actor/1'], $data['video:actor']);
        self::assertSame(['https://example.com/director/1'], $data['video:director']);
        self::assertSame(7200, $data['video:duration']);
        self::assertSame('2024-03-15T00:00:00+00:00', $data['video:release_date']);
        self::assertSame(['action'], $data['video:tag']);
    }

    /**
     * @test
     */
    public function it_renders_movie_meta_tags(): void
    {
        $og = (new OpenGraph())
            ->title('The Great Movie')
            ->type(new Movie(duration: 7200));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:type" content="video.movie">', $html);
        self::assertStringContainsString('<meta property="video:duration" content="7200">', $html);
    }
}
