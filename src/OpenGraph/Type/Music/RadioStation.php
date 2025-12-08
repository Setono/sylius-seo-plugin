<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Music;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a radio station.
 *
 * @see https://ogp.me/#type_music.radio_station
 */
final class RadioStation implements TypeInterface
{
    /**
     * @param list<string> $creators The creators of this station (profile URLs)
     */
    public function __construct(
        public readonly array $creators = [],
    ) {
    }

    public function getType(): string
    {
        return 'music.radio_station';
    }

    public function toHtml(): string
    {
        $html = [];

        foreach ($this->creators as $creator) {
            $html[] = OpenGraph::renderMetaTag('music:creator', $creator);
        }

        return implode("\n", array_filter($html, static fn ($value): bool => $value !== null));
    }
}
