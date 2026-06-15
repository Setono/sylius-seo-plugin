<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Video;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Video\TVShow;

final class TVShowTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_correct_type(): void
    {
        $tvShow = new TVShow();

        self::assertSame('video.tv_show', $tvShow->getType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_html_by_default(): void
    {
        $tvShow = new TVShow();

        self::assertSame('', $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_actors(): void
    {
        $tvShow = new TVShow(actors: [
            'https://example.com/actor/1',
            'https://example.com/actor/2',
        ]);

        $expected = '<meta property="video:actor" content="https://example.com/actor/1">' . "\n" .
            '<meta property="video:actor" content="https://example.com/actor/2">';

        self::assertSame($expected, $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_actor_roles(): void
    {
        $tvShow = new TVShow(actorRoles: ['Lead', 'Supporting']);

        $expected = '<meta property="video:actor:role" content="Lead">' . "\n" .
            '<meta property="video:actor:role" content="Supporting">';

        self::assertSame($expected, $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_directors(): void
    {
        $tvShow = new TVShow(directors: ['https://example.com/director/1']);

        self::assertSame('<meta property="video:director" content="https://example.com/director/1">', $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_writers(): void
    {
        $tvShow = new TVShow(writers: ['https://example.com/writer/1']);

        self::assertSame('<meta property="video:writer" content="https://example.com/writer/1">', $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_duration(): void
    {
        $tvShow = new TVShow(duration: 3600);

        self::assertSame('<meta property="video:duration" content="3600">', $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $tvShow = new TVShow(releaseDate: $date);

        self::assertSame('<meta property="video:release_date" content="2024-01-01T00:00:00+00:00">', $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_tags(): void
    {
        $tvShow = new TVShow(tags: ['comedy', 'sitcom']);

        $expected = '<meta property="video:tag" content="comedy">' . "\n" .
            '<meta property="video:tag" content="sitcom">';

        self::assertSame($expected, $tvShow->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_open_graph(): void
    {
        $releaseDate = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');

        $og = (new OpenGraph())
            ->title('The Great Show')
            ->type(new TVShow(
                actors: ['https://example.com/actor/1'],
                directors: ['https://example.com/director/1'],
                duration: 3600,
                releaseDate: $releaseDate,
                tags: ['comedy'],
            ));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="The Great Show">', $html);
        self::assertStringContainsString('<meta property="og:type" content="video.tv_show">', $html);
        self::assertStringContainsString('<meta property="video:actor" content="https://example.com/actor/1">', $html);
        self::assertStringContainsString('<meta property="video:director" content="https://example.com/director/1">', $html);
        self::assertStringContainsString('<meta property="video:duration" content="3600">', $html);
        self::assertStringContainsString('<meta property="video:tag" content="comedy">', $html);
    }
}
