<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type\Music;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Music\Song;

final class SongTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $song = new Song();

        self::assertSame('music.song', $song->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_properties_by_default(): void
    {
        $song = new Song();

        self::assertSame([], $song->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_duration(): void
    {
        $song = new Song(duration: 240);

        self::assertSame(['music:duration' => 240], $song->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_albums(): void
    {
        $song = new Song(albums: ['https://example.com/album/1']);

        self::assertSame(['music:album' => ['https://example.com/album/1']], $song->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_musicians(): void
    {
        $song = new Song(musicians: [
            'https://example.com/artist/1',
            'https://example.com/artist/2',
        ]);

        self::assertSame([
            'music:musician' => [
                'https://example.com/artist/1',
                'https://example.com/artist/2',
            ],
        ], $song->getProperties());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('My Song')
            ->type(new Song(duration: 240, musicians: ['https://example.com/artist/1']));

        $data = $og->toArray();

        self::assertSame('My Song', $data['og:title']);
        self::assertSame('music.song', $data['og:type']);
        self::assertSame(240, $data['music:duration']);
        self::assertSame(['https://example.com/artist/1'], $data['music:musician']);
    }
}
