<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Video;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Video\Episode;

final class EpisodeTest extends TestCase
{
    #[Test]
    public function it_returns_correct_type(): void
    {
        $episode = new Episode();

        self::assertSame('video.episode', $episode->getType());
    }

    #[Test]
    public function it_returns_empty_html_by_default(): void
    {
        $episode = new Episode();

        self::assertSame('', $episode->toHtml());
    }

    #[Test]
    public function it_renders_actors(): void
    {
        $episode = new Episode(actors: [
            'https://example.com/actor/1',
            'https://example.com/actor/2',
        ]);

        $expected = '<meta property="video:actor" content="https://example.com/actor/1">' . "\n" .
            '<meta property="video:actor" content="https://example.com/actor/2">';

        self::assertSame($expected, $episode->toHtml());
    }

    #[Test]
    public function it_renders_actor_roles(): void
    {
        $episode = new Episode(actorRoles: ['Main Character', 'Villain']);

        $expected = '<meta property="video:actor:role" content="Main Character">' . "\n" .
            '<meta property="video:actor:role" content="Villain">';

        self::assertSame($expected, $episode->toHtml());
    }

    #[Test]
    public function it_renders_directors(): void
    {
        $episode = new Episode(directors: ['https://example.com/director/1']);

        self::assertSame('<meta property="video:director" content="https://example.com/director/1">', $episode->toHtml());
    }

    #[Test]
    public function it_renders_writers(): void
    {
        $episode = new Episode(writers: ['https://example.com/writer/1']);

        self::assertSame('<meta property="video:writer" content="https://example.com/writer/1">', $episode->toHtml());
    }

    #[Test]
    public function it_renders_duration(): void
    {
        $episode = new Episode(duration: 2400);

        self::assertSame('<meta property="video:duration" content="2400">', $episode->toHtml());
    }

    #[Test]
    public function it_renders_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-03-15T00:00:00+00:00');
        $episode = new Episode(releaseDate: $date);

        self::assertSame('<meta property="video:release_date" content="2024-03-15T00:00:00+00:00">', $episode->toHtml());
    }

    #[Test]
    public function it_renders_tags(): void
    {
        $episode = new Episode(tags: ['drama', 'mystery']);

        $expected = '<meta property="video:tag" content="drama">' . "\n" .
            '<meta property="video:tag" content="mystery">';

        self::assertSame($expected, $episode->toHtml());
    }

    #[Test]
    public function it_renders_series(): void
    {
        $episode = new Episode(series: 'https://example.com/show/1');

        self::assertSame('<meta property="video:series" content="https://example.com/show/1">', $episode->toHtml());
    }

    #[Test]
    public function it_works_with_open_graph(): void
    {
        $releaseDate = new \DateTimeImmutable('2024-03-15T00:00:00+00:00');

        $og = (new OpenGraph())
            ->title('Episode 1: Pilot')
            ->type(new Episode(
                actors: ['https://example.com/actor/1'],
                directors: ['https://example.com/director/1'],
                duration: 2400,
                releaseDate: $releaseDate,
                tags: ['drama'],
                series: 'https://example.com/show/1',
            ));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="Episode 1: Pilot">', $html);
        self::assertStringContainsString('<meta property="og:type" content="video.episode">', $html);
        self::assertStringContainsString('<meta property="video:actor" content="https://example.com/actor/1">', $html);
        self::assertStringContainsString('<meta property="video:director" content="https://example.com/director/1">', $html);
        self::assertStringContainsString('<meta property="video:duration" content="2400">', $html);
        self::assertStringContainsString('<meta property="video:series" content="https://example.com/show/1">', $html);
    }
}
