<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;

/**
 * Open Graph type for articles (news, blog posts, etc.).
 *
 * @see https://ogp.me/#type_article
 */
final readonly class Article implements TypeInterface
{
    /**
     * @param list<string> $authors Writers of the article (profile URLs)
     * @param list<string> $tags Tag words associated with this article
     */
    public function __construct(
        public ?\DateTimeInterface $publishedTime = null,
        public ?\DateTimeInterface $modifiedTime = null,
        public ?\DateTimeInterface $expirationTime = null,
        public array $authors = [],
        public ?string $section = null,
        public array $tags = [],
    ) {
    }

    public function getType(): string
    {
        return 'article';
    }

    public function toHtml(): string
    {
        $html = [];

        $html[] = OpenGraph::renderMetaTag('article:published_time', $this->publishedTime?->format(\DateTimeInterface::ATOM));
        $html[] = OpenGraph::renderMetaTag('article:modified_time', $this->modifiedTime?->format(\DateTimeInterface::ATOM));
        $html[] = OpenGraph::renderMetaTag('article:expiration_time', $this->expirationTime?->format(\DateTimeInterface::ATOM));

        foreach ($this->authors as $author) {
            $html[] = OpenGraph::renderMetaTag('article:author', $author);
        }

        $html[] = OpenGraph::renderMetaTag('article:section', $this->section);

        foreach ($this->tags as $tag) {
            $html[] = OpenGraph::renderMetaTag('article:tag', $tag);
        }

        return implode("\n", array_filter($html, static fn ($value): bool => $value !== null));
    }
}
