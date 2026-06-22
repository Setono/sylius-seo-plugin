<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Music;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a music album.
 *
 * @see https://ogp.me/#type_music.album
 */
final readonly class Album implements TypeInterface
{
    /**
     * @param list<string> $songs The songs on this album (song URLs)
     * @param list<int> $songDiscs Which disc of the album a song is on
     * @param list<int> $songTracks Which track a song is
     * @param list<string> $musicians The musicians that made this album (profile URLs)
     */
    public function __construct(
        public array $songs = [],
        public array $songDiscs = [],
        public array $songTracks = [],
        public array $musicians = [],
        public ?\DateTimeInterface $releaseDate = null,
    ) {
    }

    public function getType(): string
    {
        return 'music.album';
    }

    public function toHtml(): string
    {
        $html = [];

        foreach ($this->songs as $song) {
            $html[] = OpenGraph::renderMetaTag('music:song', $song);
        }

        foreach ($this->songDiscs as $disc) {
            $html[] = OpenGraph::renderMetaTag('music:song:disc', $disc);
        }

        foreach ($this->songTracks as $track) {
            $html[] = OpenGraph::renderMetaTag('music:song:track', $track);
        }

        foreach ($this->musicians as $musician) {
            $html[] = OpenGraph::renderMetaTag('music:musician', $musician);
        }

        $html[] = OpenGraph::renderMetaTag('music:release_date', $this->releaseDate?->format(\DateTimeInterface::ATOM));

        return implode("\n", array_filter($html, static fn ($value): bool => $value !== null));
    }
}
