<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Video;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a generic video that doesn't fit other categories.
 *
 * @see https://ogp.me/#type_video.other
 */
final class Other implements TypeInterface
{
    /**
     * @param list<string> $actors Actors in the video (profile URLs)
     * @param list<string> $actorRoles The roles the actors played
     * @param list<string> $directors Directors of the video (profile URLs)
     * @param list<string> $writers Writers of the video (profile URLs)
     * @param list<string> $tags Tag words associated with this video
     */
    public function __construct(
        public readonly array $actors = [],
        public readonly array $actorRoles = [],
        public readonly array $directors = [],
        public readonly array $writers = [],
        public readonly ?int $duration = null,
        public readonly ?\DateTimeInterface $releaseDate = null,
        public readonly array $tags = [],
    ) {
    }

    public function getType(): string
    {
        return 'video.other';
    }

    public function toHtml(): string
    {
        $html = [];

        foreach ($this->actors as $actor) {
            $html[] = OpenGraph::renderMetaTag('video:actor', $actor);
        }

        foreach ($this->actorRoles as $role) {
            $html[] = OpenGraph::renderMetaTag('video:actor:role', $role);
        }

        foreach ($this->directors as $director) {
            $html[] = OpenGraph::renderMetaTag('video:director', $director);
        }

        foreach ($this->writers as $writer) {
            $html[] = OpenGraph::renderMetaTag('video:writer', $writer);
        }

        $html[] = OpenGraph::renderMetaTag('video:duration', $this->duration);
        $html[] = OpenGraph::renderMetaTag('video:release_date', $this->releaseDate?->format(\DateTimeInterface::ATOM));

        foreach ($this->tags as $tag) {
            $html[] = OpenGraph::renderMetaTag('video:tag', $tag);
        }

        return implode("\n", array_filter($html, static fn ($value): bool => $value !== null));
    }
}
