<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Music;

use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a music album.
 *
 * @see https://ogp.me/#type_music.album
 */
final class Album implements TypeInterface
{
    /**
     * @param list<string> $songs The songs on this album (song URLs)
     * @param list<int> $songDiscs Which disc of the album a song is on
     * @param list<int> $songTracks Which track a song is
     * @param list<string> $musicians The musicians that made this album (profile URLs)
     */
    public function __construct(
        public readonly array $songs = [],
        public readonly array $songDiscs = [],
        public readonly array $songTracks = [],
        public readonly array $musicians = [],
        public readonly ?\DateTimeInterface $releaseDate = null,
    ) {
    }

    public function getType(): string
    {
        return 'music.album';
    }

    public function getProperties(): array
    {
        return array_filter([
            'music:song' => [] !== $this->songs ? $this->songs : null,
            'music:song:disc' => [] !== $this->songDiscs ? $this->songDiscs : null,
            'music:song:track' => [] !== $this->songTracks ? $this->songTracks : null,
            'music:musician' => [] !== $this->musicians ? $this->musicians : null,
            'music:release_date' => $this->releaseDate?->format(\DateTimeInterface::ATOM),
        ], static fn ($value) => null !== $value);
    }
}
