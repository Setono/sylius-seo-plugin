<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Music;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Music\Album;

final class AlbumTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $album = new Album();

        self::assertSame('music.album', $album->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_html_by_default(): void
    {
        $album = new Album();

        self::assertSame('', $album->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_songs(): void
    {
        $album = new Album(songs: [
            'https://example.com/song/1',
            'https://example.com/song/2',
        ]);

        $expected = '<meta property="music:song" content="https://example.com/song/1">' . "\n" .
            '<meta property="music:song" content="https://example.com/song/2">';

        self::assertSame($expected, $album->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_song_discs(): void
    {
        $album = new Album(songDiscs: [1, 2]);

        $expected = '<meta property="music:song:disc" content="1">' . "\n" .
            '<meta property="music:song:disc" content="2">';

        self::assertSame($expected, $album->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_song_tracks(): void
    {
        $album = new Album(songTracks: [1, 5, 10]);

        $expected = '<meta property="music:song:track" content="1">' . "\n" .
            '<meta property="music:song:track" content="5">' . "\n" .
            '<meta property="music:song:track" content="10">';

        self::assertSame($expected, $album->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_musicians(): void
    {
        $album = new Album(musicians: ['https://example.com/artist/1']);

        self::assertSame('<meta property="music:musician" content="https://example.com/artist/1">', $album->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-06-15T00:00:00+00:00');
        $album = new Album(releaseDate: $date);

        self::assertSame('<meta property="music:release_date" content="2024-06-15T00:00:00+00:00">', $album->toHtml());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $releaseDate = new \DateTimeImmutable('2024-06-15T00:00:00+00:00');

        $og = (new OpenGraph())
            ->title('Greatest Hits')
            ->type(new Album(
                songs: ['https://example.com/song/1'],
                musicians: ['https://example.com/artist/1'],
                releaseDate: $releaseDate,
            ));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="Greatest Hits">', $html);
        self::assertStringContainsString('<meta property="og:type" content="music.album">', $html);
        self::assertStringContainsString('<meta property="music:song" content="https://example.com/song/1">', $html);
        self::assertStringContainsString('<meta property="music:musician" content="https://example.com/artist/1">', $html);
        self::assertStringContainsString('<meta property="music:release_date" content="2024-06-15T00:00:00+00:00">', $html);
    }
}
