<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Music;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Music\Song;

final class SongTest extends TestCase
{
    #[Test]
    public function it_returns_correct_type(): void
    {
        $song = new Song();

        self::assertSame('music.song', $song->getType());
    }

    #[Test]
    public function it_returns_empty_html_by_default(): void
    {
        $song = new Song();

        self::assertSame('', $song->toHtml());
    }

    #[Test]
    public function it_renders_duration(): void
    {
        $song = new Song(duration: 240);

        self::assertSame('<meta property="music:duration" content="240">', $song->toHtml());
    }

    #[Test]
    public function it_renders_albums(): void
    {
        $song = new Song(albums: ['https://example.com/album/1']);

        self::assertSame('<meta property="music:album" content="https://example.com/album/1">', $song->toHtml());
    }

    #[Test]
    public function it_renders_musicians(): void
    {
        $song = new Song(musicians: [
            'https://example.com/artist/1',
            'https://example.com/artist/2',
        ]);

        $expected = '<meta property="music:musician" content="https://example.com/artist/1">' . "\n" .
            '<meta property="music:musician" content="https://example.com/artist/2">';

        self::assertSame($expected, $song->toHtml());
    }

    #[Test]
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('My Song')
            ->type(new Song(duration: 240, musicians: ['https://example.com/artist/1']));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="My Song">', $html);
        self::assertStringContainsString('<meta property="og:type" content="music.song">', $html);
        self::assertStringContainsString('<meta property="music:duration" content="240">', $html);
        self::assertStringContainsString('<meta property="music:musician" content="https://example.com/artist/1">', $html);
    }
}
