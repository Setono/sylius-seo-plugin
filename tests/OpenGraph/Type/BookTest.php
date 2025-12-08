<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Book;

final class BookTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $book = new Book();

        self::assertSame('book', $book->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_properties_by_default(): void
    {
        $book = new Book();

        self::assertSame([], $book->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_authors(): void
    {
        $book = new Book(authors: ['https://example.com/profile/author']);

        self::assertSame(['book:author' => ['https://example.com/profile/author']], $book->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_isbn(): void
    {
        $book = new Book(isbn: '978-3-16-148410-0');

        self::assertSame(['book:isbn' => '978-3-16-148410-0'], $book->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-06-15T00:00:00+00:00');
        $book = new Book(releaseDate: $date);

        self::assertSame(['book:release_date' => '2024-06-15T00:00:00+00:00'], $book->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_tags(): void
    {
        $book = new Book(tags: ['fiction', 'thriller']);

        self::assertSame(['book:tag' => ['fiction', 'thriller']], $book->getProperties());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('The Great Book')
            ->type(new Book(isbn: '978-3-16-148410-0'));

        $data = $og->toArray();

        self::assertSame('The Great Book', $data['og:title']);
        self::assertSame('book', $data['og:type']);
        self::assertSame('978-3-16-148410-0', $data['book:isbn']);
    }
}
