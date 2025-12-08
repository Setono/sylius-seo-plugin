<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type\Video;

use Setono\SyliusSEOPlugin\OpenGraph\Type\TypeInterface;

/**
 * Open Graph type for a TV show episode.
 *
 * @see https://ogp.me/#type_video.episode
 */
final class Episode implements TypeInterface
{
    /**
     * @param list<string> $actors Actors in the episode (profile URLs)
     * @param list<string> $actorRoles The roles the actors played
     * @param list<string> $directors Directors of the episode (profile URLs)
     * @param list<string> $writers Writers of the episode (profile URLs)
     * @param list<string> $tags Tag words associated with this episode
     */
    public function __construct(
        public readonly array $actors = [],
        public readonly array $actorRoles = [],
        public readonly array $directors = [],
        public readonly array $writers = [],
        public readonly ?int $duration = null,
        public readonly ?\DateTimeInterface $releaseDate = null,
        public readonly array $tags = [],
        public readonly ?string $series = null,
    ) {
    }

    public function getType(): string
    {
        return 'video.episode';
    }

    public function getProperties(): array
    {
        return array_filter([
            'video:actor' => [] !== $this->actors ? $this->actors : null,
            'video:actor:role' => [] !== $this->actorRoles ? $this->actorRoles : null,
            'video:director' => [] !== $this->directors ? $this->directors : null,
            'video:writer' => [] !== $this->writers ? $this->writers : null,
            'video:duration' => $this->duration,
            'video:release_date' => $this->releaseDate?->format(\DateTimeInterface::ATOM),
            'video:tag' => [] !== $this->tags ? $this->tags : null,
            'video:series' => $this->series,
        ], static fn ($value) => null !== $value);
    }
}
