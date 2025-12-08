<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Music;

use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a single song.
 *
 * @see https://ogp.me/#type_music.song
 */
final class Song implements TypeInterface
{
    /**
     * @param list<string> $albums The albums this song is from (album URLs)
     * @param list<int> $albumDiscs Which disc of the album this song is on
     * @param list<int> $albumTracks Which track this song is on
     * @param list<string> $musicians The musicians that made this song (profile URLs)
     */
    public function __construct(
        public readonly ?int $duration = null,
        public readonly array $albums = [],
        public readonly array $albumDiscs = [],
        public readonly array $albumTracks = [],
        public readonly array $musicians = [],
    ) {
    }

    public function getType(): string
    {
        return 'music.song';
    }

    public function getProperties(): array
    {
        return array_filter([
            'music:duration' => $this->duration,
            'music:album' => [] !== $this->albums ? $this->albums : null,
            'music:album:disc' => [] !== $this->albumDiscs ? $this->albumDiscs : null,
            'music:album:track' => [] !== $this->albumTracks ? $this->albumTracks : null,
            'music:musician' => [] !== $this->musicians ? $this->musicians : null,
        ], static fn ($value) => null !== $value);
    }
}
