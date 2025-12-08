<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

/**
 * Open Graph type for articles (news, blog posts, etc.).
 *
 * @see https://ogp.me/#type_article
 */
final class Article implements TypeInterface
{
    /**
     * @param list<string> $authors Writers of the article (profile URLs)
     * @param list<string> $tags Tag words associated with this article
     */
    public function __construct(
        public readonly ?\DateTimeInterface $publishedTime = null,
        public readonly ?\DateTimeInterface $modifiedTime = null,
        public readonly ?\DateTimeInterface $expirationTime = null,
        public readonly array $authors = [],
        public readonly ?string $section = null,
        public readonly array $tags = [],
    ) {
    }

    public function getType(): string
    {
        return 'article';
    }

    public function getProperties(): array
    {
        return array_filter([
            'article:published_time' => $this->publishedTime?->format(\DateTimeInterface::ATOM),
            'article:modified_time' => $this->modifiedTime?->format(\DateTimeInterface::ATOM),
            'article:expiration_time' => $this->expirationTime?->format(\DateTimeInterface::ATOM),
            'article:author' => [] !== $this->authors ? $this->authors : null,
            'article:section' => $this->section,
            'article:tag' => [] !== $this->tags ? $this->tags : null,
        ], static fn ($value) => null !== $value);
    }
}
