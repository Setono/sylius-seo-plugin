<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Video;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a TV show episode.
 *
 * @see https://ogp.me/#type_video.episode
 */
final readonly class Episode implements TypeInterface
{
    /**
     * @param list<string> $actors Actors in the episode (profile URLs)
     * @param list<string> $actorRoles The roles the actors played
     * @param list<string> $directors Directors of the episode (profile URLs)
     * @param list<string> $writers Writers of the episode (profile URLs)
     * @param list<string> $tags Tag words associated with this episode
     */
    public function __construct(
        public array $actors = [],
        public array $actorRoles = [],
        public array $directors = [],
        public array $writers = [],
        public ?int $duration = null,
        public ?\DateTimeInterface $releaseDate = null,
        public array $tags = [],
        public ?string $series = null,
    ) {
    }

    public function getType(): string
    {
        return 'video.episode';
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

        $html[] = OpenGraph::renderMetaTag('video:series', $this->series);

        return implode("\n", array_filter($html, static fn ($value): bool => $value !== null));
    }
}
