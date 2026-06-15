<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Video;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Video\Other;

final class OtherTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_correct_type(): void
    {
        $other = new Other();

        self::assertSame('video.other', $other->getType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_html_by_default(): void
    {
        $other = new Other();

        self::assertSame('', $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_actors(): void
    {
        $other = new Other(actors: [
            'https://example.com/actor/1',
            'https://example.com/actor/2',
        ]);

        $expected = '<meta property="video:actor" content="https://example.com/actor/1">' . "\n" .
            '<meta property="video:actor" content="https://example.com/actor/2">';

        self::assertSame($expected, $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_actor_roles(): void
    {
        $other = new Other(actorRoles: ['Presenter', 'Guest']);

        $expected = '<meta property="video:actor:role" content="Presenter">' . "\n" .
            '<meta property="video:actor:role" content="Guest">';

        self::assertSame($expected, $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_directors(): void
    {
        $other = new Other(directors: ['https://example.com/director/1']);

        self::assertSame('<meta property="video:director" content="https://example.com/director/1">', $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_writers(): void
    {
        $other = new Other(writers: ['https://example.com/writer/1']);

        self::assertSame('<meta property="video:writer" content="https://example.com/writer/1">', $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_duration(): void
    {
        $other = new Other(duration: 600);

        self::assertSame('<meta property="video:duration" content="600">', $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-05-20T00:00:00+00:00');
        $other = new Other(releaseDate: $date);

        self::assertSame('<meta property="video:release_date" content="2024-05-20T00:00:00+00:00">', $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_tags(): void
    {
        $other = new Other(tags: ['tutorial', 'howto']);

        $expected = '<meta property="video:tag" content="tutorial">' . "\n" .
            '<meta property="video:tag" content="howto">';

        self::assertSame($expected, $other->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_open_graph(): void
    {
        $releaseDate = new \DateTimeImmutable('2024-05-20T00:00:00+00:00');

        $og = (new OpenGraph())
            ->title('How to Code')
            ->type(new Other(
                actors: ['https://example.com/actor/1'],
                duration: 600,
                releaseDate: $releaseDate,
                tags: ['tutorial'],
            ));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="How to Code">', $html);
        self::assertStringContainsString('<meta property="og:type" content="video.other">', $html);
        self::assertStringContainsString('<meta property="video:actor" content="https://example.com/actor/1">', $html);
        self::assertStringContainsString('<meta property="video:duration" content="600">', $html);
        self::assertStringContainsString('<meta property="video:tag" content="tutorial">', $html);
    }
}
