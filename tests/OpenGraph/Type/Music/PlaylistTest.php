<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Music;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Music\Playlist;

final class PlaylistTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $playlist = new Playlist();

        self::assertSame('music.playlist', $playlist->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_html_by_default(): void
    {
        $playlist = new Playlist();

        self::assertSame('', $playlist->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_songs(): void
    {
        $playlist = new Playlist(songs: [
            'https://example.com/song/1',
            'https://example.com/song/2',
        ]);

        $expected = '<meta property="music:song" content="https://example.com/song/1">' . "\n" .
            '<meta property="music:song" content="https://example.com/song/2">';

        self::assertSame($expected, $playlist->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_song_discs(): void
    {
        $playlist = new Playlist(songDiscs: [1, 2]);

        $expected = '<meta property="music:song:disc" content="1">' . "\n" .
            '<meta property="music:song:disc" content="2">';

        self::assertSame($expected, $playlist->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_song_tracks(): void
    {
        $playlist = new Playlist(songTracks: [1, 5]);

        $expected = '<meta property="music:song:track" content="1">' . "\n" .
            '<meta property="music:song:track" content="5">';

        self::assertSame($expected, $playlist->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_creators(): void
    {
        $playlist = new Playlist(creators: ['https://example.com/profile/1']);

        self::assertSame('<meta property="music:creator" content="https://example.com/profile/1">', $playlist->toHtml());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('My Playlist')
            ->type(new Playlist(
                songs: ['https://example.com/song/1'],
                creators: ['https://example.com/profile/1'],
            ));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="My Playlist">', $html);
        self::assertStringContainsString('<meta property="og:type" content="music.playlist">', $html);
        self::assertStringContainsString('<meta property="music:song" content="https://example.com/song/1">', $html);
        self::assertStringContainsString('<meta property="music:creator" content="https://example.com/profile/1">', $html);
    }
}
