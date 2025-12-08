<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;

/**
 * Open Graph type for books.
 *
 * @see https://ogp.me/#type_book
 */
final class Book implements TypeInterface
{
    /**
     * @param list<string> $authors Who wrote this book (profile URLs)
     * @param list<string> $tags Tag words associated with this book
     */
    public function __construct(
        public readonly array $authors = [],
        public readonly ?string $isbn = null,
        public readonly ?\DateTimeInterface $releaseDate = null,
        public readonly array $tags = [],
    ) {
    }

    public function getType(): string
    {
        return 'book';
    }

    public function toHtml(): string
    {
        $html = [];

        foreach ($this->authors as $author) {
            $html[] = OpenGraph::renderMetaTag('book:author', $author);
        }

        $html[] = OpenGraph::renderMetaTag('book:isbn', $this->isbn);
        $html[] = OpenGraph::renderMetaTag('book:release_date', $this->releaseDate?->format(\DateTimeInterface::ATOM));

        foreach ($this->tags as $tag) {
            $html[] = OpenGraph::renderMetaTag('book:tag', $tag);
        }

        return implode("\n", array_filter($html, static fn ($value): bool => $value !== null));
    }
}
