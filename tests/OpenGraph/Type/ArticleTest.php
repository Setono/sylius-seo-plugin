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
    public function it_returns_empty_properties_by_default(): void
    {
        $article = new Article();

        self::assertSame([], $article->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_published_time(): void
    {
        $date = new \DateTimeImmutable('2024-01-15T10:30:00+00:00');
        $article = new Article(publishedTime: $date);

        self::assertSame(['article:published_time' => '2024-01-15T10:30:00+00:00'], $article->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_modified_time(): void
    {
        $date = new \DateTimeImmutable('2024-01-16T14:00:00+00:00');
        $article = new Article(modifiedTime: $date);

        self::assertSame(['article:modified_time' => '2024-01-16T14:00:00+00:00'], $article->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_expiration_time(): void
    {
        $date = new \DateTimeImmutable('2024-12-31T23:59:59+00:00');
        $article = new Article(expirationTime: $date);

        self::assertSame(['article:expiration_time' => '2024-12-31T23:59:59+00:00'], $article->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_authors(): void
    {
        $article = new Article(authors: [
            'https://example.com/profile/john',
            'https://example.com/profile/jane',
        ]);

        self::assertSame([
            'article:author' => [
                'https://example.com/profile/john',
                'https://example.com/profile/jane',
            ],
        ], $article->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_section(): void
    {
        $article = new Article(section: 'Technology');

        self::assertSame(['article:section' => 'Technology'], $article->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_properties_with_tags(): void
    {
        $article = new Article(tags: ['php', 'symfony', 'open-graph']);

        self::assertSame(['article:tag' => ['php', 'symfony', 'open-graph']], $article->getProperties());
    }

    /**
     * @test
     */
    public function it_returns_all_properties(): void
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

        $expected = [
            'article:published_time' => '2024-01-15T10:30:00+00:00',
            'article:modified_time' => '2024-01-16T14:00:00+00:00',
            'article:author' => ['https://example.com/profile/john'],
            'article:section' => 'Technology',
            'article:tag' => ['php'],
        ];

        self::assertSame($expected, $article->getProperties());
    }

    /**
     * @test
     */
    public function it_works_with_open_graph(): void
    {
        $og = (new OpenGraph())
            ->title('My Article')
            ->type(new Article(section: 'Technology'));

        $data = $og->toArray();

        self::assertSame('My Article', $data['og:title']);
        self::assertSame('article', $data['og:type']);
        self::assertSame('Technology', $data['article:section']);
    }

    /**
     * @test
     */
    public function it_renders_article_meta_tags(): void
    {
        $og = (new OpenGraph())
            ->title('My Article')
            ->type(new Article(section: 'Technology'));

        $html = $og->toHtml();

        self::assertStringContainsString('<meta property="og:type" content="article">', $html);
        self::assertStringContainsString('<meta property="article:section" content="Technology">', $html);
    }
}
