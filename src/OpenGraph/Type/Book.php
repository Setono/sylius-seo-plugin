<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\OpenGraph\Type;

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

    public function getProperties(): array
    {
        return array_filter([
            'book:author' => [] !== $this->authors ? $this->authors : null,
            'book:isbn' => $this->isbn,
            'book:release_date' => $this->releaseDate?->format(\DateTimeInterface::ATOM),
            'book:tag' => [] !== $this->tags ? $this->tags : null,
        ], static fn ($value) => null !== $value);
    }
}
