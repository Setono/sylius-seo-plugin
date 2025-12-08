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
    public function it_returns_empty_html_by_default(): void
    {
        $book = new Book();

        self::assertSame('', $book->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_authors(): void
    {
        $book = new Book(authors: ['https://example.com/profile/author']);

        self::assertSame('<meta property="book:author" content="https://example.com/profile/author">', $book->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_isbn(): void
    {
        $book = new Book(isbn: '978-3-16-148410-0');

        self::assertSame('<meta property="book:isbn" content="978-3-16-148410-0">', $book->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_release_date(): void
    {
        $date = new \DateTimeImmutable('2024-06-15T00:00:00+00:00');
        $book = new Book(releaseDate: $date);

        self::assertSame('<meta property="book:release_date" content="2024-06-15T00:00:00+00:00">', $book->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_tags(): void
    {
        $book = new Book(tags: ['fiction', 'thriller']);

        $expected = '<meta property="book:tag" content="fiction">' . "\n" .
            '<meta property="book:tag" content="thriller">';

        self::assertSame($expected, $book->toHtml());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('The Great Book')
            ->type(new Book(isbn: '978-3-16-148410-0'));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="The Great Book">', $html);
        self::assertStringContainsString('<meta property="og:type" content="book">', $html);
        self::assertStringContainsString('<meta property="book:isbn" content="978-3-16-148410-0">', $html);
    }
}
