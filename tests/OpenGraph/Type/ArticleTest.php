<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\OpenGraph\Type;

use PHPUnit\Framework\TestCase;
use Setono\SyliusSEOPlugin\OpenGraph\OpenGraph;
use Setono\SyliusSEOPlugin\OpenGraph\Type\Article;

final class ArticleTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_type(): void
    {
        $article = new Article();

        self::assertSame('article', $article->getType());
    }

    /**
     * @test
     */
    public function it_returns_empty_html_by_default(): void
    {
        $article = new Article();

        self::assertSame('', $article->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_published_time(): void
    {
        $date = new \DateTimeImmutable('2024-01-15T10:30:00+00:00');
        $article = new Article(publishedTime: $date);

        self::assertSame('<meta property="article:published_time" content="2024-01-15T10:30:00+00:00">', $article->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_modified_time(): void
    {
        $date = new \DateTimeImmutable('2024-01-16T14:00:00+00:00');
        $article = new Article(modifiedTime: $date);

        self::assertSame('<meta property="article:modified_time" content="2024-01-16T14:00:00+00:00">', $article->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_expiration_time(): void
    {
        $date = new \DateTimeImmutable('2024-12-31T23:59:59+00:00');
        $article = new Article(expirationTime: $date);

        self::assertSame('<meta property="article:expiration_time" content="2024-12-31T23:59:59+00:00">', $article->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_authors(): void
    {
        $article = new Article(authors: [
            'https://example.com/profile/john',
            'https://example.com/profile/jane',
        ]);

        $expected = '<meta property="article:author" content="https://example.com/profile/john">' . "\n" .
            '<meta property="article:author" content="https://example.com/profile/jane">';

        self::assertSame($expected, $article->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_section(): void
    {
        $article = new Article(section: 'Technology');

        self::assertSame('<meta property="article:section" content="Technology">', $article->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_tags(): void
    {
        $article = new Article(tags: ['php', 'symfony', 'open-graph']);

        $expected = '<meta property="article:tag" content="php">' . "\n" .
            '<meta property="article:tag" content="symfony">' . "\n" .
            '<meta property="article:tag" content="open-graph">';

        self::assertSame($expected, $article->toHtml());
    }

    /**
     * @test
     */
    public function it_renders_all_properties(): void
    {
        $publishedTime = new \DateTimeImmutable('2024-01-15T10:30:00+00:00');
        $modifiedTime = new \DateTimeImmutable('2024-01-16T14:00:00+00:00');

        $article = new Article(
            publishedTime: $publishedTime,
            modifiedTime: $modifiedTime,
            authors: ['https://example.com/profile/john'],
            section: 'Technology',
            tags: ['php'],
        );

        $html = $article->toHtml();

        self::assertStringContainsString('<meta property="article:published_time" content="2024-01-15T10:30:00+00:00">', $html);
        self::assertStringContainsString('<meta property="article:modified_time" content="2024-01-16T14:00:00+00:00">', $html);
        self::assertStringContainsString('<meta property="article:author" content="https://example.com/profile/john">', $html);
        self::assertStringContainsString('<meta property="article:section" content="Technology">', $html);
        self::assertStringContainsString('<meta property="article:tag" content="php">', $html);
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('My Article')
            ->type(new Article(section: 'Technology'));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:title" content="My Article">', $html);
        self::assertStringContainsString('<meta property="og:type" content="article">', $html);
        self::assertStringContainsString('<meta property="article:section" content="Technology">', $html);
    }
}
