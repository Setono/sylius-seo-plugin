<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Music;

use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a music playlist.
 *
 * @see https://ogp.me/#type_music.playlist
 */
final class Playlist implements TypeInterface
{
    /**
     * @param list<string> $songs The songs in this playlist (song URLs)
     * @param list<int> $songDiscs Which disc of the album a song is on
     * @param list<int> $songTracks Which track a song is
     * @param list<string> $creators The creators of this playlist (profile URLs)
     */
    public function __construct(
        public readonly array $songs = [],
        public readonly array $songDiscs = [],
        public readonly array $songTracks = [],
        public readonly array $creators = [],
    ) {
    }

    public function getType(): string
    {
        return 'music.playlist';
    }

    public function getProperties(): array
    {
        return array_filter([
            'music:song' => [] !== $this->songs ? $this->songs : null,
            'music:song:disc' => [] !== $this->songDiscs ? $this->songDiscs : null,
            'music:song:track' => [] !== $this->songTracks ? $this->songTracks : null,
            'music:creator' => [] !== $this->creators ? $this->creators : null,
        ], static fn ($value) => null !== $value);
    }
}
