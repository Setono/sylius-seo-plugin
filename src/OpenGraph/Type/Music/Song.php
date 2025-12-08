<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Music;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
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

    public function toHtml(): string
    {
        $html = [];

        $html[] = OpenGraph::renderMetaTag('music:duration', $this->duration);

        foreach ($this->albums as $album) {
            $html[] = OpenGraph::renderMetaTag('music:album', $album);
        }

        foreach ($this->albumDiscs as $disc) {
            $html[] = OpenGraph::renderMetaTag('music:album:disc', $disc);
        }

        foreach ($this->albumTracks as $track) {
            $html[] = OpenGraph::renderMetaTag('music:album:track', $track);
        }

        foreach ($this->musicians as $musician) {
            $html[] = OpenGraph::renderMetaTag('music:musician', $musician);
        }

        return implode("\n", array_filter($html, static fn ($value): bool => $value !== null));
    }
}
